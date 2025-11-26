/**
 * MySuperTax Theme Configuration
 * Professional tax consulting color palette and design tokens
 */

export const themeConfig = {
  // Brand Colors
  brand: {
    primary: {
      50: '#eff6ff',
      100: '#dbeafe',
      200: '#bfdbfe',
      300: '#93c5fd',
      400: '#60a5fa',
      500: '#3b82f6',
      600: '#1e40af',
      700: '#1e3a8a',
      800: '#1e3a8a',
      900: '#1e2a5a',
      950: '#0f172a',
    },
    secondary: {
      50: '#f0fdfa',
      100: '#ccfbf1',
      200: '#99f6e4',
      300: '#5eead4',
      400: '#2dd4bf',
      500: '#14b8a6',
      600: '#0d9488',
      700: '#0f766e',
      800: '#115e59',
      900: '#134e4a',
      950: '#042f2e',
    },
    accent: {
      50: '#fffbeb',
      100: '#fef3c7',
      200: '#fde68a',
      300: '#fcd34d',
      400: '#fbbf24',
      500: '#f59e0b',
      600: '#d97706',
      700: '#b45309',
      800: '#92400e',
      900: '#78350f',
      950: '#451a03',
    }
  },

  // Status Colors
  status: {
    success: {
      50: '#f0fdf4',
      100: '#dcfce7',
      200: '#bbf7d0',
      300: '#86efac',
      400: '#4ade80',
      500: '#22c55e',
      600: '#16a34a',
      700: '#15803d',
      800: '#166534',
      900: '#14532d',
      950: '#052e16',
    },
    warning: {
      50: '#fffbeb',
      100: '#fef3c7',
      200: '#fde68a',
      300: '#fcd34d',
      400: '#fbbf24',
      500: '#f59e0b',
      600: '#d97706',
      700: '#b45309',
      800: '#92400e',
      900: '#78350f',
      950: '#451a03',
    },
    error: {
      50: '#fef2f2',
      100: '#fee2e2',
      200: '#fecaca',
      300: '#fca5a5',
      400: '#f87171',
      500: '#ef4444',
      600: '#dc2626',
      700: '#b91c1c',
      800: '#991b1b',
      900: '#7f1d1d',
      950: '#450a0a',
    },
    info: {
      50: '#eff6ff',
      100: '#dbeafe',
      200: '#bfdbfe',
      300: '#93c5fd',
      400: '#60a5fa',
      500: '#3b82f6',
      600: '#2563eb',
      700: '#1d4ed8',
      800: '#1e40af',
      900: '#1e3a8a',
      950: '#172554',
    }
  },

  // Neutral Colors
  neutral: {
    50: '#fafaf9',
    100: '#f5f5f4',
    200: '#e7e5e4',
    300: '#d6d3d1',
    400: '#a8a29e',
    500: '#78716c',
    600: '#57534e',
    700: '#44403c',
    800: '#292524',
    900: '#1c1917',
    950: '#0c0a09',
  },

  // Typography
  typography: {
    fontFamily: {
      primary: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
      secondary: ['Merriweather', 'Georgia', 'serif'],
      mono: ['JetBrains Mono', 'Fira Code', 'Consolas', 'monospace'],
    },
    fontSize: {
      xs: '0.75rem',
      sm: '0.875rem',
      base: '1rem',
      lg: '1.125rem',
      xl: '1.25rem',
      '2xl': '1.5rem',
      '3xl': '1.875rem',
      '4xl': '2.25rem',
    },
    fontWeight: {
      normal: '400',
      medium: '500',
      semibold: '600',
      bold: '700',
    }
  },

  // Spacing
  spacing: {
    xs: '0.25rem',
    sm: '0.5rem',
    md: '1rem',
    lg: '1.5rem',
    xl: '2rem',
    '2xl': '3rem',
    '3xl': '4rem',
  },

  // Border Radius
  borderRadius: {
    sm: '0.25rem',
    md: '0.375rem',
    lg: '0.5rem',
    xl: '0.75rem',
    '2xl': '1rem',
    full: '9999px',
  },

  // Shadows
  boxShadow: {
    sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
    md: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
    lg: '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
    xl: '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
    '2xl': '0 25px 50px -12px rgb(0 0 0 / 0.25)',
  },

  // Transitions
  transition: {
    fast: '150ms ease-in-out',
    normal: '300ms ease-in-out',
    slow: '500ms ease-in-out',
  },

  // Component Specific
  components: {
    header: {
      height: '4rem',
    },
    sidebar: {
      width: '16rem',
      collapsedWidth: '4rem',
    },
  },

  // Gradients
  gradients: {
    primary: 'linear-gradient(135deg, var(--color-primary-600), var(--color-secondary-600))',
    accent: 'linear-gradient(135deg, var(--color-accent-500), var(--color-accent-600))',
    neutral: 'linear-gradient(135deg, var(--color-neutral-100), var(--color-neutral-200))',
  }
}

// Helper functions for theme usage
export const getColor = (colorPath) => {
  const keys = colorPath.split('.')
  let value = themeConfig
  
  for (const key of keys) {
    value = value[key]
    if (!value) return null
  }
  
  return value
}

export const getCSSVariable = (colorPath) => {
  const color = colorPath.replace('.', '-')
  return `var(--color-${color})`
}

// Professional button variants
export const buttonVariants = {
  primary: {
    base: 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
    gradient: 'bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800',
  },
  secondary: {
    base: 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 border border-neutral-300',
    outline: 'border border-primary-300 text-primary-700 hover:bg-primary-50',
  },
  success: {
    base: 'bg-success-600 text-white hover:bg-success-700 focus:ring-success-500',
  },
  warning: {
    base: 'bg-warning-600 text-white hover:bg-warning-700 focus:ring-warning-500',
  },
  error: {
    base: 'bg-error-600 text-white hover:bg-error-700 focus:ring-error-500',
  }
}

// Status badge variants
export const badgeVariants = {
  success: 'bg-success-100 text-success-800',
  warning: 'bg-warning-100 text-warning-800',
  error: 'bg-error-100 text-error-800',
  info: 'bg-info-100 text-info-800',
  neutral: 'bg-neutral-100 text-neutral-800',
}

export default themeConfig