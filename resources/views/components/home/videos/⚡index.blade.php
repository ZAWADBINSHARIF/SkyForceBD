<?php

use App\Models\AdsVideo;
use Livewire\Component;

new class extends Component
{
    public array $vidoes = [];

    public function mount()
    {
        $vidoes = AdsVideo::first();

        if ($vidoes)
            $this->vidoes = $vidoes->video;
    }
};
?>

<div class="pb-12">
    <style>
        .vc-track::-webkit-scrollbar {
            display: none;
        }

        .vc-card {
            flex: 0 0 160px;
            cursor: pointer;
            border-radius: 12px;
            background: #fff;
            border: 0.5px solid #e5e7eb;
            overflow: hidden;
            will-change: flex-basis, opacity;
            align-self: center;
        }

        .vc-thumb {
            position: relative;
            aspect-ratio: 16/9;
            background: #f3f4f6;
            overflow: hidden;
        }

        .vc-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .vc-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vc-play {
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.93);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vc-info {
            padding: 8px 10px 10px;
            overflow: hidden;
        }

        .vc-title {
            font-weight: 500;
            color: #6b7280;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
            margin: 0;
        }

        .vc-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            border: 0.5px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            color: #6b7280;
            transition: background 0.15s;
            outline: none;
        }

        .vc-nav:hover {
            background: #f9fafb;
        }

        @media (max-width: 640px) {
            .vc-track {
                height: 280px !important;
            }

            .vc-nav {
                display: none;
            }
        }
    </style>

    <div x-data="{
            rawVideos: @js($vidoes),
            REPS: 7,
            BASE_W: 160,
            BASE_FS: 11,    CTR_FS: 14,
            BASE_PLAY: 32,  CTR_PLAY: 50,
            BASE_PLAY_ICO: 13, CTR_PLAY_ICO: 20,
            BASE_OPACITY: 1,
            GAP: 14,

            get videos() {
                const out = [];
                for (let i = 0; i < this.REPS; i++)
                    for (const v of this.rawVideos) out.push(v);
                return out;
            },

            getThumb(url) {
                if (url.includes('youtube.com') || url.includes('youtu.be')) {
                    const m = url.match(/(?:youtu\.be\/|youtube\.com\/(?:.*v=|\/v\/|embed\/))([^?& ]+)/);
                    return m ? `https://img.youtube.com/vi/${m[1]}/maxresdefault.jpg` : '';
                }
                return 'https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=500&q=80';
            },

            lerp(a, b, t) { return a + (b - a) * t; },

            isMobile() { return window.innerWidth <= 640; },

            applyScales() {
                const track = this.$refs.track;
                const cards = Array.from(track.querySelectorAll('.vc-card'));
                const trackRect = track.getBoundingClientRect();
                const cx = trackRect.left + trackRect.width / 2;
                const halfTrack = trackRect.width / 2;
                const mobile = this.isMobile();

                const centerW = mobile ? trackRect.width * 0.78 : 300;
                const baseW   = mobile ? trackRect.width * 0.42 : this.BASE_W;

                let closestIdx = 0, minDist = Infinity;

                cards.forEach((card, i) => {
                    const r = card.getBoundingClientRect();
                    const cardCx = r.left + r.width / 2;
                    const dist = Math.abs(cardCx - cx);
                    if (dist < minDist) { minDist = dist; closestIdx = i; }

                    const t = Math.max(0, 1 - dist / (halfTrack * 0.75));

                    const w       = Math.round(this.lerp(baseW,               centerW,              t));
                    const opacity = this.lerp(this.BASE_OPACITY, 1,            t);
                    const fs      = this.lerp(this.BASE_FS,      this.CTR_FS,  t);
                    const playW   = this.lerp(this.BASE_PLAY,    this.CTR_PLAY, t);
                    const icoW    = this.lerp(this.BASE_PLAY_ICO, this.CTR_PLAY_ICO, t);
                    const shadow  = t > 0.4
                        ? `0 ${Math.round(t*8)}px ${Math.round(t*24)}px rgba(0,0,0,${(t*0.13).toFixed(2)})`
                        : 'none';
                    const borderC = t > 0.4
                        ? `rgba(100,100,100,${(0.15 + t*0.25).toFixed(2)})`
                        : '#e5e7eb';

                    card.style.cssText = `
                        flex: 0 0 ${w}px;
                        opacity: ${opacity.toFixed(3)};
                        box-shadow: ${shadow};
                        border: 0.5px solid ${borderC};
                        border-radius: 12px;
                        cursor: pointer;
                        overflow: hidden;
                        background: #fff;
                        align-self: center;
                    `;

                    const playEl = card.querySelector('.vc-play');
                    if (playEl) {
                        playEl.style.width  = playW.toFixed(1) + 'px';
                        playEl.style.height = playW.toFixed(1) + 'px';
                    }
                    const icoEl = card.querySelector('.vc-play-ico');
                    if (icoEl) {
                        icoEl.setAttribute('width',  icoW.toFixed(1));
                        icoEl.setAttribute('height', icoW.toFixed(1));
                    }
                    const titleEl = card.querySelector('.vc-title');
                    if (titleEl) {
                        titleEl.style.fontSize = fs.toFixed(2) + 'px';
                        titleEl.style.color = t > 0.6 ? '#111827' : '#6b7280';
                    }
                });

                const len = this.rawVideos.length;
                const lo = len, hi = len * (this.REPS - 1);
                if (closestIdx < lo || closestIdx >= hi) {
                    const jumpCards = hi - lo;
                    const approxW = this.BASE_W + this.GAP;
                    if (closestIdx < lo) track.scrollLeft += jumpCards * approxW;
                    else                 track.scrollLeft -= jumpCards * approxW;
                }
            },

            findCenterIdx() {
                const track = this.$refs.track;
                const cards = Array.from(track.querySelectorAll('.vc-card'));
                const cx = track.getBoundingClientRect().left + track.clientWidth / 2;
                let minDist = Infinity, idx = 0;
                cards.forEach((c, i) => {
                    const r = c.getBoundingClientRect();
                    const dist = Math.abs(r.left + r.width / 2 - cx);
                    if (dist < minDist) { minDist = dist; idx = i; }
                });
                return idx;
            },

            scrollToCenterIdx(idx) {
                const track = this.$refs.track;
                const cards = Array.from(track.querySelectorAll('.vc-card'));
                const card = cards[idx]; if (!card) return;
                const trackRect = track.getBoundingClientRect();
                const cardRect  = card.getBoundingClientRect();
                const offset = cardRect.left - trackRect.left - (trackRect.width / 2) + (cardRect.width / 2);
                track.scrollBy({ left: offset, behavior: 'smooth' });
            },

            prev() { this.scrollToCenterIdx(this.findCenterIdx() - 1); },
            next() { this.scrollToCenterIdx(this.findCenterIdx() + 1); },

            ticking: false,
            onScroll() {
                if (!this.ticking) {
                    requestAnimationFrame(() => { this.applyScales(); this.ticking = false; });
                    this.ticking = true;
                }
            },

            init() {
                this.$nextTick(() => {
                    this.scrollToCenterIdx(this.rawVideos.length * 3);
                    setTimeout(() => this.applyScales(), 80);
                });
            }
        }">
        <div class="relative" style="padding: 0 8px;">
            <button @click="prev()" class="vc-nav" style="left: -14px;" aria-label="Previous">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button @click="next()" class="vc-nav" style="right: -14px;" aria-label="Next">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div x-ref="track" @scroll="onScroll()" class="vc-track flex items-center overflow-x-auto"
                style="gap: 14px; height: 320px; padding: 0 12px; scrollbar-width: none; -ms-overflow-style: none;">
                <template x-for="(v, i) in videos" :key="i">
                    <div class="vc-card" @click="window.open(v.url, '_blank')">
                        <div class="vc-thumb">
                            <img :src="getThumb(v.url)" :alt="v.title" loading="lazy">
                            <div class="vc-overlay">
                                <div class="vc-play" style="width: 32px; height: 32px;">
                                    <svg class="vc-play-ico" width="13" height="13" fill="#534AB7" viewBox="0 0 24 24"
                                        style="margin-left: 2px;">
                                        <path d="M8 5v14l11-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="vc-info">
                            <p class="vc-title" style="font-size: 11px;" x-text="v.title"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>