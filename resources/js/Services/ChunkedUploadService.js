import axios from 'axios'

const CHUNK_SIZE = 2 * 1024 * 1024 // 5MB chunks
const DB_NAME = 'DocumentUploadDB'
const STORE_NAME = 'uploads'

export class ChunkedUploadService {
  static async initDB() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(DB_NAME, 1)
      
      request.onerror = () => reject(request.error)
      request.onsuccess = () => resolve(request.result)
      
      request.onupgradeneeded = (event) => {
        const db = event.target.result
        if (!db.objectStoreNames.contains(STORE_NAME)) {
          db.createObjectStore(STORE_NAME, { keyPath: 'fileId' })
        }
      }
    })
  }

  static getEndpoint(endpoint) {
    // Determine if we're in client or admin context
    const path = window.location.pathname
    const isClient = path.startsWith('/client')
    const prefix = isClient ? '/client' : '/admin'
    return `${prefix}${endpoint}`
  }

  static async saveProgress(fileId, uploadSessionId, uploadedChunks, totalChunks, fileData) {
    const db = await this.initDB()
    return new Promise((resolve, reject) => {
      const transaction = db.transaction([STORE_NAME], 'readwrite')
      const store = transaction.objectStore(STORE_NAME)
      
      store.put({
        fileId,
        uploadSessionId,
        uploadedChunks,
        totalChunks,
        fileData,
        timestamp: Date.now()
      })
      
      transaction.oncomplete = () => resolve()
      transaction.onerror = () => reject(transaction.error)
    })
  }

  static async getProgress(fileId) {
    const db = await this.initDB()
    return new Promise((resolve, reject) => {
      const transaction = db.transaction([STORE_NAME], 'readonly')
      const store = transaction.objectStore(STORE_NAME)
      const request = store.get(fileId)
      
      request.onsuccess = () => resolve(request.result)
      request.onerror = () => reject(request.error)
    })
  }

  static async clearProgress(fileId) {
    const db = await this.initDB()
    return new Promise((resolve, reject) => {
      const transaction = db.transaction([STORE_NAME], 'readwrite')
      const store = transaction.objectStore(STORE_NAME)
      const request = store.delete(fileId)
      
      request.onsuccess = () => resolve()
      request.onerror = () => reject(request.error)
    })
  }

  static async uploadFile(file, clientId, documentType, taxYear, notes, onProgress, onStatusChange) {
    const fileId = `${Date.now()}_${Math.random().toString(36).substr(2, 9)}`
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE)
    let uploadSessionId = null
    let uploadedChunks = 0

    try {
      // Check for existing progress
      const existingProgress = await this.getProgress(fileId)
      if (existingProgress) {
        uploadSessionId = existingProgress.uploadSessionId
        uploadedChunks = existingProgress.uploadedChunks
        onStatusChange('resuming')
      } else {
        // Initialize upload session
        onStatusChange('initializing')
        const initEndpoint = this.getEndpoint('/documents/upload/init')
        const initResponse = await axios.post(initEndpoint, {
          file_name: file.name,
          file_size: file.size,
          file_type: file.type,
          file_id: fileId,
          total_chunks: totalChunks,
          client_id: clientId,
          document_type: documentType,
          tax_year: taxYear,
          notes: notes
        })

        uploadSessionId = initResponse.data.upload_session_id
      }

      // Upload remaining chunks
      onStatusChange('uploading')
      for (let chunkIndex = uploadedChunks; chunkIndex < totalChunks; chunkIndex++) {
        const start = chunkIndex * CHUNK_SIZE
        const end = Math.min(start + CHUNK_SIZE, file.size)
        const chunk = file.slice(start, end)

        const formData = new FormData()
        formData.append('chunk', chunk)
        formData.append('chunk_index', chunkIndex)
        formData.append('total_chunks', totalChunks)
        formData.append('upload_session_id', uploadSessionId)
        formData.append('file_id', fileId)

        try {
          const chunkEndpoint = this.getEndpoint('/documents/upload/chunk')
          await axios.post(chunkEndpoint, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: (progressEvent) => {
              const chunkProgress = progressEvent.loaded / progressEvent.total
              const totalProgress = ((chunkIndex + chunkProgress) / totalChunks) * 100
              onProgress(totalProgress)
            }
          })

          uploadedChunks = chunkIndex + 1
          
          // Save progress
          await this.saveProgress(fileId, uploadSessionId, uploadedChunks, totalChunks, {
            name: file.name,
            size: file.size,
            type: file.type,
            clientId,
            documentType,
            taxYear,
            notes
          })
        } catch (chunkError) {
          // Save progress before throwing
          await this.saveProgress(fileId, uploadSessionId, uploadedChunks, totalChunks, {
            name: file.name,
            size: file.size,
            type: file.type,
            clientId,
            documentType,
            taxYear,
            notes
          })
          throw chunkError
        }
      }

      // Finalize upload
      onStatusChange('finalizing')
      const finalizeEndpoint = this.getEndpoint('/documents/upload/finalize')
      const finalResponse = await axios.post(finalizeEndpoint, {
        upload_session_id: uploadSessionId,
        file_id: fileId
      })

      // Clear progress
      await this.clearProgress(fileId)
      onStatusChange('completed')

      return finalResponse.data
    } catch (error) {
      console.error('Chunked upload failed:', error)
      onStatusChange('paused')
      throw error
    }
  }

  static async uploadMultipleFiles(files, clientId, documentType, taxYear, notes, onProgress, onStatusChange) {
    const results = []
    
    for (let i = 0; i < files.length; i++) {
      const file = files[i]
      
      try {
        const result = await this.uploadFile(
          file,
          clientId,
          documentType,
          taxYear,
          notes,
          (progress) => {
            const overallProgress = ((i + progress / 100) / files.length) * 100
            onProgress(overallProgress, file.name)
          },
          (status) => {
            onStatusChange(status, file.name)
          }
        )
        results.push({ success: true, file: file.name, data: result })
      } catch (error) {
        results.push({ success: false, file: file.name, error: error.message })
      }
    }
    
    return results
  }

  static async getUploadSessions() {
    const db = await this.initDB()
    return new Promise((resolve, reject) => {
      const transaction = db.transaction([STORE_NAME], 'readonly')
      const store = transaction.objectStore(STORE_NAME)
      const request = store.getAll()
      
      request.onsuccess = () => resolve(request.result)
      request.onerror = () => reject(request.error)
    })
  }
}
