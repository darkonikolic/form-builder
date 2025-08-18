// Design tokens and constants to replace magic numbers
export const SIZES = {
    xs: '3', // 12px
    sm: '4', // 16px
    md: '5', // 20px
    lg: '6', // 24px
    xl: '8', // 32px
    '2xl': '10', // 40px
    '3xl': '12', // 48px
    '4xl': '16', // 64px
    '5xl': '20', // 80px
    '6xl': '24', // 96px
    '7xl': '28', // 112px
};

export const SPACING = {
    xs: '1', // 4px
    sm: '2', // 8px
    md: '3', // 12px
    lg: '4', // 16px
    xl: '6', // 24px
    '2xl': '8', // 32px
    '3xl': '12', // 48px
    '4xl': '16', // 64px
};

export const RADIUS = {
    none: 'none',
    sm: 'sm',
    md: 'md',
    lg: 'lg',
    xl: 'xl',
    full: 'full',
};

export const SHADOWS = {
    none: 'none',
    sm: 'shadow-sm',
    md: 'shadow',
    lg: 'shadow-lg',
    xl: 'shadow-xl',
    '2xl': 'shadow-2xl',
};

// Component-specific constants
export const BUTTON_SIZES = {
    icon: {
        sm: `w-${SIZES.sm} h-${SIZES.sm}`,
        md: `w-${SIZES.md} h-${SIZES.md}`,
        lg: `w-${SIZES.lg} h-${SIZES.lg}`,
    },
    text: {
        sm: `px-${SPACING.sm} py-${SPACING.xs}`,
        md: `px-${SPACING.md} py-${SPACING.sm}`,
        lg: `px-${SPACING.lg} py-${SPACING.md}`,
    },
};

export const ICON_SIZES = {
    sm: `h-${SIZES.xs} w-${SIZES.xs}`,
    md: `h-${SIZES.sm} w-${SIZES.sm}`,
    lg: `h-${SIZES.md} w-${SIZES.md}`,
    xl: `h-${SIZES.lg} w-${SIZES.lg}`,
};
