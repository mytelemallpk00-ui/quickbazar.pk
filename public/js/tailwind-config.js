tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        beautage: {
                            purple: 'var(--qb-navy)',
                            purpleDark: 'var(--qb-navy-deep)',
                            purpleLight: 'var(--qb-soft)',
                            purpleBorder: 'var(--qb-line)',
                            pink: 'var(--qb-orange)',
                            pinkHover: 'var(--qb-orange-hover)',
                            pinkLight: 'var(--qb-soft)',
                            amber: 'var(--qb-highlight)',
                            gold: 'var(--qb-highlight)',
                            slate: 'var(--qb-navy)'
                        },
                        brand: {
                            navy: '#0B132B',
                            dark: '#1C2541',
                            gold: '#D4AF37',
                            lightgold: '#F4E8C1',
                            accent: '#4361EE',
                            light: '#F8F9FA'
                        }
                    },
                    keyframes: {
                        marquee: {
                            '0%': { transform: 'translateX(0%)' },
                            '100%': { transform: 'translateX(-50%)' }
                        },
                        pulseGlow: {
                            '0%, 100%': { boxShadow: '0 0 15px rgba(230, 0, 126, 0.35)' },
                            '50%': { boxShadow: '0 0 25px rgba(230, 0, 126, 0.65)' }
                        }
                    },
                    animation: {
                        marquee: 'marquee 25s linear infinite',
                        pulseGlow: 'pulseGlow 2.5s infinite',
                    }
                }
            }
        }
