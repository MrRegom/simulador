document.addEventListener('DOMContentLoaded', () => {
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });

    // Sticky Header Effect
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.style.padding = '10px 0';
            header.style.background = 'rgba(11, 11, 11, 0.95)';
        } else {
            header.style.padding = '20px 0';
            header.style.background = 'rgba(11, 11, 11, 0.8)';
        }
    });

    // Smooth Scroll for Navigation Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Hero carousel

    const heroCarousel = document.querySelector('.hero-carousel');
    if (heroCarousel) {
        initHeroCarousel(heroCarousel);
    }

    // Ranking section
    const rankingSection = document.querySelector('#ranking');
    if (rankingSection) {
        initRanking(rankingSection);
    }

    // Console Branding
    console.log('%c A4R SIMRACING ', 'background: #E10600; color: #fff; font-size: 20px; font-weight: bold;');
    console.log('%c Vive la experiencia F1 ', 'color: #E10600; font-size: 14px;');
});

function initRanking(section) {
    const apiBase = section.dataset.apiBase || '/a4r';
    const state = {
        range: '30d',
        track: null,
        limit: 10,
        total: 0
    };

    const rangeButtons = Array.from(section.querySelectorAll('.range-btn'));
    const trackSelect = section.querySelector('#trackSelect');
    const recentTracks = section.querySelector('#recentTracks');
    const frequentTracks = section.querySelector('#frequentTracks');
    const rankingList = section.querySelector('#rankingList');
    const rankingEmpty = section.querySelector('#rankingEmpty');
    const loadMoreRanking = section.querySelector('#loadMoreRanking');
    const rankingBanner = section.querySelector('.ranking-banner span');
    const trackTitle = section.querySelector('#trackTitle');
    const trackMeta = section.querySelector('#trackMeta');
    const lastUpdated = section.querySelector('#lastUpdated');
    const statusText = section.querySelector('#statusText');
    const activeTrackName = section.querySelector('#activeTrackName');
    const activeTrackMeta = section.querySelector('#activeTrackMeta');
    const activeTrackBest = section.querySelector('#activeTrackBest');
    const activeTrackPilot = section.querySelector('#activeTrackPilot');
    const activeTrackLabel = section.querySelector('#activeTrackLabel');
    const activePilotAvatar = section.querySelector('#activePilotAvatar');
    const podium1 = section.querySelector('#podium1');
    const podium2 = section.querySelector('#podium2');
    const podium3 = section.querySelector('#podium3');
    const soundToggle = section.querySelector('#soundToggle');
    const enterRanking = section.querySelector('#enterRanking');

    let lastTopSignature = null;
    let soundEnabled = false;
    let audioContext = null;

    if (enterRanking) {
        const seen = sessionStorage.getItem('rankingIntroSeen') === '1';
        if (seen) {
            section.classList.remove('intro');
        }
        enterRanking.addEventListener('click', () => {
            section.classList.remove('intro');
            sessionStorage.setItem('rankingIntroSeen', '1');
        });
    }

    const dateFormatter = new Intl.DateTimeFormat('es-CL', {
        day: '2-digit',
        month: 'short'
    });

    const fullFormatter = new Intl.DateTimeFormat('es-CL', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    const setStatus = (text, ok = true) => {
        statusText.textContent = text;
        statusText.parentElement.classList.toggle('status-error', !ok);
    };

    const formatDate = (value) => {
        if (!value) return '--';
        const date = new Date(value.replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) return value;
        return dateFormatter.format(date);
    };

    const formatFullDate = (value) => {
        if (!value) return '--';
        const date = new Date(value.replace(' ', 'T'));
        if (Number.isNaN(date.getTime())) return value;
        return fullFormatter.format(date);
    };

    const ensureAudio = () => {
        if (!audioContext) {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }
    };

    const playPodiumSound = () => {
        if (!soundEnabled) return;
        ensureAudio();
        const duration = 0.2;
        const now = audioContext.currentTime;
        const osc = audioContext.createOscillator();
        const gain = audioContext.createGain();
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(440, now);
        osc.frequency.exponentialRampToValueAtTime(660, now + duration);
        gain.gain.setValueAtTime(0.001, now);
        gain.gain.exponentialRampToValueAtTime(0.15, now + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.001, now + duration);
        osc.connect(gain);
        gain.connect(audioContext.destination);
        osc.start(now);
        osc.stop(now + duration);
    };

    if (soundToggle) {
        soundToggle.addEventListener('click', () => {
            soundEnabled = !soundEnabled;
            soundToggle.classList.toggle('active', soundEnabled);
            soundToggle.textContent = soundEnabled ? 'Sonido ON' : 'Sonido OFF';
            if (soundEnabled) {
                playPodiumSound();
            }
        });
    }

        const getInitials = (name) => {
        if (!name) return '--';
        return name.split(' ').filter(Boolean).slice(0, 2).map(part => part[0].toUpperCase()).join('');
    };

const buildChip = (item, selectedTrack) => {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'chip';
        chip.textContent = item.pista;
        if (item.pista === selectedTrack) {
            chip.classList.add('active');
        }
        chip.addEventListener('click', () => {
            state.track = item.pista;
            loadRanking();
        });
        return chip;
    };

    const renderTrackSelect = (tracks, selectedTrack) => {
        trackSelect.innerHTML = '';
        tracks.forEach(track => {
            const option = document.createElement('option');
            option.value = track.pista;
            option.textContent = `${track.pista} (${track.total})`;
            if (track.pista === selectedTrack) option.selected = true;
            trackSelect.appendChild(option);
        });
    };

    const renderTrackLists = (data) => {
        recentTracks.innerHTML = '';
        frequentTracks.innerHTML = '';

        (data.recent_tracks || []).forEach(item => {
            recentTracks.appendChild(buildChip(item, data.selected_track));
        });

        (data.frequent_tracks || []).forEach(item => {
            frequentTracks.appendChild(buildChip(item, data.selected_track));
        });
    };

    const renderPodium = (top) => {
        const fillCard = (card, item) => {
            if (!card) return;
            const name = item?.piloto_nombre || 'Piloto';
            const time = item?.tiempo || '--:--.---';
            const avatar = card.querySelector('.podium-avatar');
            const nameEl = card.querySelector('.podium-name');
            const timeEl = card.querySelector('.podium-time');

            if (nameEl) nameEl.textContent = name;
            if (timeEl) timeEl.textContent = time;

            if (avatar) {
                if (item?.foto_path) {
                    avatar.style.backgroundImage = `url(${item.foto_path.startsWith('http') ? item.foto_path : `${apiBase}/${item.foto_path}`})`;
                    avatar.textContent = '';
                    avatar.classList.add('has-photo');
                } else {
                    avatar.style.backgroundImage = 'none';
                    avatar.textContent = getInitials(name);
                    avatar.classList.remove('has-photo');
                }
            }
        };

        fillCard(podium1, top[0]);
        fillCard(podium2, top[1]);
        fillCard(podium3, top[2]);
    };

    const renderRanking = (data) => {
        rankingList.innerHTML = '';
        const top = data.top || [];

        if (!top.length) {
            rankingEmpty.classList.remove('hidden');
            return;
        }

        rankingEmpty.classList.add('hidden');
        renderPodium(top);

        const bestMs = top[0]?.tiempo_ms || null;

        top.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'rank-row';
            row.style.animationDelay = `${index * 0.04}s`;
            if (index === 0) {
                row.classList.add('leader');
            }
            if (index < 3) {
                row.classList.add('top-three');
            }

            const pos = String(index + 1).padStart(2, '0');
            const name = item.piloto_nombre || 'Piloto sin nombre';
            const rut = '';
            const time = item.tiempo || '--:--.---';
            // Fecha disponible si se necesita en el futuro

            const avatar = document.createElement('div');
            avatar.className = 'rank-avatar';
            if (item.foto_path) {
                avatar.style.backgroundImage = `url(${item.foto_path.startsWith('http') ? item.foto_path : `${apiBase}/${item.foto_path}`})`;
                avatar.textContent = '';
            } else {
                avatar.style.backgroundImage = 'none';
                avatar.textContent = getInitials(name);
            }

            const width = bestMs && item.tiempo_ms ? Math.max(35, Math.min(100, (bestMs / item.tiempo_ms) * 100)) : 100;

            row.innerHTML = `
                <div class="rank-pos">${pos}</div>
                <div class="rank-name">
                    <strong>${name}</strong>
                </div>
                <div class="rank-time">${time}</div>
                <div class="rank-bar"><div class="rank-bar-fill" style="width: ${width}%"></div></div>
            `;

            row.prepend(avatar);
            rankingList.appendChild(row);
        });
    };

    const applyRangeActive = () => {
        rangeButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.range === state.range);
        });
    };

    async function loadRanking() {
        applyRangeActive();
        setStatus('Actualizando ranking...', true);

        const params = new URLSearchParams({ range: state.range, limit: Math.min(state.limit, 50) });
        if (state.track) params.set('track', state.track);

        try {
            const response = await fetch(`${apiBase}/api/ranking/track_summary?${params.toString()}`, {
                cache: 'no-store'
            });
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.error || 'No se pudo cargar el ranking');
            }

            state.track = data.selected_track || state.track;

            trackTitle.textContent = data.selected_track || 'Sin pista';
            const info = (data.tracks || []).find(track => track.pista === data.selected_track);
            if (info) {
                trackMeta.textContent = `${info.total} registros \u00B7 \u00DAltimo: ${formatDate(info.last_at)}`;
                state.total = Number(info.total) || data.top.length;
            } else {
                trackMeta.textContent = 'Sin registros recientes';
                state.total = data.top.length;
            }

            lastUpdated.textContent = `Actualizado: ${formatFullDate(data.last_updated)}`;
            if (rankingBanner) {
                const currentLimit = Math.min(state.limit, data.top.length || state.limit, 50);
                rankingBanner.textContent = `Ranking en vivo \u00B7 Top ${currentLimit}`;
            }

            const activeName = data.active_track || data.selected_track || '--';
            activeTrackName.textContent = activeName;
            if (activeTrackLabel) activeTrackLabel.textContent = activeName;
            activeTrackMeta.textContent = data.active_track ? 'Activa ahora' : 'Sin pista activa';
            const activeBest = data.active_best || (data.active_track === data.selected_track ? (data.top || [])[0] : null);
            if (activeBest) {
                activeTrackBest.textContent = activeBest.tiempo || '--:--.---';
                activeTrackPilot.textContent = activeBest.piloto_nombre || 'Piloto';
                if (activePilotAvatar) {
                    if (activeBest.foto_path) {
                        activePilotAvatar.style.backgroundImage = `url(${activeBest.foto_path.startsWith('http') ? activeBest.foto_path : `${apiBase}/${activeBest.foto_path}`})`;
                        activePilotAvatar.textContent = '';
                    } else {
                        activePilotAvatar.style.backgroundImage = 'none';
                        activePilotAvatar.textContent = getInitials(activeBest.piloto_nombre || 'Piloto');
                    }
                }
            } else {
                activeTrackBest.textContent = '--:--.---';
                activeTrackPilot.textContent = '--';
                if (activePilotAvatar) {
                    activePilotAvatar.style.backgroundImage = 'none';
                    activePilotAvatar.textContent = '--';
                }
            }

            if (data.tracks && data.tracks.length) {
                renderTrackSelect(data.tracks, data.selected_track);
            }

            renderTrackLists(data);
            renderRanking(data);

            if (loadMoreRanking) {
                const effectiveTotal = Math.min(state.total || 0, 50);
                const canLoadMore = effectiveTotal > state.limit;
                loadMoreRanking.classList.toggle('hidden', !canLoadMore);
                if (canLoadMore) {
                    loadMoreRanking.textContent = `Ver m\u00E1s (${effectiveTotal - state.limit})`;
                }
            }

            const currentTop = data.top && data.top[0];
            if (currentTop) {
                const signature = `${currentTop.piloto_nombre || ''}-${currentTop.tiempo_ms || ''}`;
                if (lastTopSignature && signature !== lastTopSignature) {
                    playPodiumSound();
                }
                lastTopSignature = signature;
            }

            setStatus('Ranking actualizado', true);
        } catch (error) {
            console.error(error);
            setStatus('Error al cargar ranking', false);
            rankingList.innerHTML = '';
            rankingEmpty.classList.remove('hidden');
        }
    }

    rangeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            state.range = btn.dataset.range;
            state.limit = 10;
            loadRanking();
        });
    });

    trackSelect.addEventListener('change', (event) => {
        state.track = event.target.value;
        state.limit = 10;
        loadRanking();
    });

    if (loadMoreRanking) {
        loadMoreRanking.addEventListener('click', () => {
            state.limit = Math.min(state.total || 50, 50);
            loadRanking();
        });
    }

    loadRanking();
    setInterval(loadRanking, 30000);
}

function initHeroCarousel(container) {
    const slides = Array.from(container.querySelectorAll('.hero-slide'));
    if (slides.length < 2) return;
    let idx = 0;
    setInterval(() => {
        slides[idx].classList.remove('active');
        idx = (idx + 1) % slides.length;
        slides[idx].classList.add('active');
    }, 5000);
}
