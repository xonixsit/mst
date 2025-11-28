/**
 * SSN Masking Utilities for Security
 * Masks SSN display while preserving functionality
 */

/**
 * Mask SSN for display (show only last 4 digits)
 * @param {string} ssn - The SSN to mask
 * @returns {string} - Masked SSN (***-**-1234)
 */
export function maskSSN(ssn) {
  if (!ssn || typeof ssn !== 'string') {
    return ''
  }
  
  // Remove any non-digit characters
  const digits = ssn.replace(/\D/g, '')
  
  if (digits.length === 9) {
    // Show only last 4 digits: ***-**-1234
    return `***-**-${digits.slice(-4)}`
  } else if (digits.length >= 4) {
    // For partial SSNs, mask what we can
    const lastFour = digits.slice(-4)
    const maskedLength = Math.max(0, digits.length - 4)
    const masked = '*'.repeat(maskedLength)
    
    if (digits.length >= 7) {
      // Format as ***-**-1234
      return `${masked.slice(0, 3)}-${masked.slice(3, 5)}-${lastFour}`
    } else if (digits.length >= 5) {
      // Format as ***-**
      return `${masked.slice(0, 3)}-${masked.slice(3)}${lastFour}`
    } else {
      // Just show masked + last digits
      return masked + lastFour
    }
  }
  
  // For very short inputs, don't mask
  return ssn
}

/**
 * Check if SSN should be masked (has enough digits)
 * @param {string} ssn - The SSN to check
 * @returns {boolean} - Whether SSN should be masked
 */
export function shouldMaskSSN(ssn) {
  if (!ssn || typeof ssn !== 'string') {
    return false
  }
  
  const digits = ssn.replace(/\D/g, '')
  return digits.length >= 4
}

/**
 * Format SSN with dashes as user types
 * @param {string} value - Current input value
 * @returns {string} - Formatted SSN
 */
export function formatSSNInput(value) {
  if (!value) return ''
  
  // Remove all non-digit characters
  let digits = value.replace(/\D/g, '')
  
  // Limit to 9 digits
  digits = digits.substring(0, 9)
  
  // Format with dashes
  if (digits.length >= 6) {
    return `${digits.slice(0, 3)}-${digits.slice(3, 5)}-${digits.slice(5)}`
  } else if (digits.length >= 4) {
    return `${digits.slice(0, 3)}-${digits.slice(3)}`
  } else {
    return digits
  }
}

/**
 * Unmask SSN to get the actual value
 * @param {string} maskedSSN - The masked SSN
 * @param {string} originalSSN - The original unmasked SSN
 * @returns {string} - The actual SSN value
 */
export function unmaskSSN(maskedSSN, originalSSN) {
  // If the masked SSN contains asterisks, return the original
  if (maskedSSN && maskedSSN.includes('*')) {
    return originalSSN || ''
  }
  
  // Otherwise, return the current value (user is editing)
  return maskedSSN || ''
}

/**
 * Validate SSN format
 * @param {string} ssn - The SSN to validate
 * @returns {boolean} - Whether SSN is valid
 */
export function isValidSSN(ssn) {
  if (!ssn || typeof ssn !== 'string') {
    return false
  }
  
  // Check if it matches the pattern XXX-XX-XXXX
  const ssnPattern = /^\d{3}-\d{2}-\d{4}$/
  return ssnPattern.test(ssn)
}

/**
 * Get display value for SSN (masked or unmasked based on focus state)
 * @param {string} ssn - The actual SSN value
 * @param {boolean} isFocused - Whether the field is focused
 * @param {boolean} hasValue - Whether there's a value to display
 * @returns {string} - The value to display
 */
export function getSSNDisplayValue(ssn, isFocused, hasValue) {
  if (!hasValue || !ssn) {
    return ''
  }
  
  // Show actual value when focused for editing
  if (isFocused) {
    return ssn
  }
  
  // Show masked value when not focused
  return shouldMaskSSN(ssn) ? maskSSN(ssn) : ssn
}