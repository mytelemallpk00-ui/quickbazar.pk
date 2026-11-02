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
                            purple: '#5D267D',
                            purpleDark: '#451B5C',
                            purpleLight: '#FAF5FF',
                            purpleBorder: '#E9CCE8',
                            pink: '#E6007E',
                            pinkHover: '#BF0068',
                            pinkLight: '#FDF2F8',
                            amber: '#F5A623',
                            gold: '#F5A623',
                            slate: '#1E1B4B'
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
