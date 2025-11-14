/**
 * Phone number masking utilities
 */

/**
 * Format phone number as user types
 * @param {string} value - Raw phone number input
 * @returns {string} - Formatted phone number
 */
export function formatPhoneNumber(value) {
  // Remove all non-digit characters
  const digits = value.replace(/\D/g, '');
  
  // Don't format if empty
  if (!digits) return '';
  
  // Format based on length
  if (digits.length <= 3) {
    return `(${digits}`;
  } else if (digits.length <= 6) {
    return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
  } else {
    return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6, 10)}`;
  }
}

/**
 * Clean phone number for storage (remove formatting)
 * @param {string} value - Formatted phone number
 * @returns {string} - Clean digits only
 */
export function cleanPhoneNumber(value) {
  return value.replace(/\D/g, '');
}

/**
 * Validate phone number format
 * @param {string} value - Phone number to validate
 * @returns {boolean} - True if valid
 */
export function isValidPhoneNumber(value) {
  const digits = cleanPhoneNumber(value);
  return digits.length === 10;
}

/**
 * Get display format for phone number
 * @param {string} value - Raw phone number
 * @returns {string} - Formatted for display
 */
export function getPhoneDisplayFormat(value) {
  if (!value) return '';
  
  const digits = cleanPhoneNumber(value);
  
  if (digits.length === 10) {
    return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6)}`;
  }
  
  return value; // Return as-is if not 10 digits
}

/**
 * Handle phone input with masking
 * @param {Event} event - Input event
 * @param {Function} callback - Callback function to update value
 */
export function handlePhoneInput(event, callback) {
  const input = event.target;
  const cursorPosition = input.selectionStart;
  const oldValue = input.value;
  const oldLength = oldValue.length;
  
  // Format the new value
  const formattedValue = formatPhoneNumber(input.value);
  
  // Update the input value
  input.value = formattedValue;
  
  // Calculate new cursor position
  const newLength = formattedValue.length;
  const lengthDiff = newLength - oldLength;
  let newCursorPosition = cursorPosition + lengthDiff;
  
  // Adjust cursor position for special cases
  if (lengthDiff > 0) {
    // If we added characters (formatting), adjust cursor
    if (formattedValue[cursorPosition] === ')' || 
        formattedValue[cursorPosition] === ' ' || 
        formattedValue[cursorPosition] === '-') {
      newCursorPosition = cursorPosition + 1;
    }
  }
  
  // Set cursor position
  setTimeout(() => {
    input.setSelectionRange(newCursorPosition, newCursorPosition);
  }, 0);
  
  // Call the callback with the formatted value
  if (callback) {
    callback(formattedValue);
  }
}