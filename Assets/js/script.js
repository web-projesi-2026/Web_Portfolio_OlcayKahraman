/**
 * script.js — Olcay KAHRAMAN Portfolio
 * Tüm sayfalarda ortak çalışır.
 * Backend: PHP + MySQL (api/ klasörü)
 */

// =============================================
// GLOBAL API YARDIMCISI
// =============================================
async function apiFetch(url, options = {}) {
    const res = await fetch(url, {
        headers: { 'Content-Type': 'application/json' },
        ...options
    });
    return res.json();
}

// =============================================
// 1. TEMA YÖNETİMİ (localStorage — tema tercihi sunucusuz saklanabilir)
// =============================================
(function initTheme() {
    const saved = localStorage.getItem('ok_theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
})();

document.addEventListener('DOMContentLoaded', () => {
    const themeCheckbox = document.getElementById('theme-checkbox');
    const htmlEl = document.documentElement;
    const saved  = localStorage.getItem('ok_theme') || 'light';

    htmlEl.setAttribute('data-theme', saved);
    if (themeCheckbox) themeCheckbox.checked = (saved === 'dark');

    themeCheckbox?.addEventListener('change', () => {
        const t = themeCheckbox.checked ? 'dark' : 'light';
        htmlEl.setAttribute('data-theme', t);
        localStorage.setItem('ok_theme', t);
    });

    // =============================================
    // 2. HAMBURGEr MENÜ
    // =============================================
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const navMenu      = document.getElementById('nav-menu');

    hamburgerBtn?.addEventListener('click', e => {
        e.stopPropagation();
        navMenu?.classList.toggle('active');
    });

    document.addEventListener('click', e => {
        if (navMenu?.classList.contains('active') &&
            !navMenu.contains(e.target) &&
            e.target !== hamburgerBtn) {
            navMenu.classList.remove('active');
        }
    });

    navMenu?.querySelectorAll('a').forEach(a =>
        a.addEventListener('click', () => navMenu.classList.remove('active'))
    );

    // =============================================
    // 3. AKTİF NAV LİNKİ
    // =============================================
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('nav ul li a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && currentPage === href) {
            link.classList.add('active');
        }
    });

    // =============================================
    // 4. PROFİL FOTOĞRAFI BÜYÜTME
    // =============================================
    const profileBtn  = document.getElementById('profile-trigger');
    const overlay     = document.getElementById('photo-overlay');
    const expandedImg = document.getElementById('expanded-photo');

    profileBtn?.addEventListener('click', e => {
        e.stopPropagation();
        const src = profileBtn.querySelector('img')?.src;
        if (src && overlay && expandedImg) {
            expandedImg.src = src;
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    });

    const closeLightbox = () => {
        overlay?.classList.remove('active');
        document.body.style.overflow = '';
    };
    overlay?.addEventListener('click', e => { if (e.target === overlay) closeLightbox(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

    // =============================================
    // 5. SCROLL TO TOP
    // =============================================
    const scrollBtn = document.getElementById('scrollToTopBtn');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.style.display = window.scrollY > 400 ? 'flex' : 'none';
        });
        scrollBtn.addEventListener('click', () =>
            window.scrollTo({ top: 0, behavior: 'smooth' })
        );
    }

    // =============================================
    // 6. TOAST BİLDİRİMİ
    // =============================================
    let toastTimer;
    window.showToast = function(msg, type = 'default') {
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'toast-notification';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.style.background = type === 'error' ? 'var(--danger)' : 'var(--accent-color)';
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
    };

    // =============================================
    // 7. GİRİŞ YAP KISMI KALDIRILDI
    // =============================================

    // =============================================
    // 8. PROJE SAYFASI
    // =============================================
    initProjectsPage();

    // =============================================
    // 9. İLETİŞİM FORMU
    // =============================================
    initContactForm();

}); // DOMContentLoaded sonu


// =============================================
// PROJE SAYFASI — api/projects.php
// =============================================
function initProjectsPage() {
    const projectContainer = document.getElementById('dynamic-projects');
    if (!projectContainer) return;

    let allProjects  = [];   // DB'den gelen ham veri
    let activeFilter = 'all';
    let activeSearch = '';
    let activeSort   = 'default';

    const searchInput    = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    const filterChips    = document.querySelectorAll('.chip');
    const sortSelect     = document.getElementById('sort-select');
    const resultsInfo    = document.getElementById('results-info');
    const favCountEl     = document.getElementById('fav-count');

    // Favoriler hâlâ localStorage'da (kullanıcı bazlı, sunucu gerektirmez)
    function getFavs()    { return JSON.parse(localStorage.getItem('ok_favorites') || '[]'); }
    function saveFavs(f)  { localStorage.setItem('ok_favorites', JSON.stringify(f)); }

    function toggleFavorite(id, title) {
        let favs = getFavs();
        const idx = favs.indexOf(id);
        let added;
        if (idx === -1) { favs.push(id); added = true; }
        else            { favs.splice(idx, 1); added = false; }
        saveFavs(favs);
        render();
        window.showToast(added
            ? `⭐ "${title}" favorilere eklendi!`
            : `❌ "${title}" favorilerden çıkarıldı.`);
    }

    function getFilteredSorted() {
        const favs = getFavs();
        let list   = [...allProjects];

        if (activeSearch) {
            const q = activeSearch.toLowerCase();
            list = list.filter(p =>
                p.title.toLowerCase().includes(q) ||
                p.description.toLowerCase().includes(q) ||
                (Array.isArray(p.tags) ? p.tags : []).some(t => t.toLowerCase().includes(q))
            );
        }

        if (activeFilter !== 'all') {
            list = list.filter(p => p.category === activeFilter);
        }

        if (activeSort === 'az') list.sort((a, b) => a.title.localeCompare(b.title, 'tr'));
        else if (activeSort === 'za') list.sort((a, b) => b.title.localeCompare(a.title, 'tr'));
        else if (activeSort === 'fav') {
            list.sort((a, b) => {
                const af = favs.includes(Number(a.id)) ? 0 : 1;
                const bf = favs.includes(Number(b.id)) ? 0 : 1;
                return af - bf;
            });
        }
        return list;
    }

    function render() {
        const list = getFilteredSorted();
        const favs = getFavs();
        const totalFavs = favs.filter(id => allProjects.some(p => Number(p.id) === id)).length;

        if (favCountEl) favCountEl.textContent = `${totalFavs} Favori`;
        if (resultsInfo) resultsInfo.textContent = list.length > 0 ? `${list.length} proje gösteriliyor` : '';

        projectContainer.innerHTML = '';

        if (list.length === 0) {
            projectContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <p>Aramanızla eşleşen proje bulunamadı.</p>
                </div>`;
            return;
        }

        list.forEach((p, i) => {
            const id    = Number(p.id);
            const isFav = favs.includes(id);
            const tags  = Array.isArray(p.tags) ? p.tags : [];
            const card  = document.createElement('div');
            card.className = `project-frame${isFav ? ' is-favorite' : ''}`;
            card.dataset.id = id;
            card.style.animationDelay = `${i * 0.07}s`;

            card.innerHTML = `
                <span class="project-badge">${escHtml(p.badge)}</span>
                <img src="${escHtml(p.image)}" alt="${escHtml(p.title)}" class="project-image" loading="lazy"
                     onerror="this.style.background='var(--led-color)';this.style.minHeight='200px'">
                <div class="project-overlay">
                    <h3>${escHtml(p.title)}</h3>
                    <p class="project-description">${escHtml(p.description)}</p>
                    <div class="project-tags">
                        ${tags.map(t => `<span>${escHtml(t)}</span>`).join('')}
                    </div>
                    <div class="card-actions">
                        <button class="view-btn${isFav ? ' favorited' : ''}"
                            data-id="${id}" data-title="${escHtml(p.title)}"
                            aria-label="${isFav ? 'Favorilerden Çıkar' : 'Favorilere Ekle'}">
                            <i class="${isFav ? 'fas' : 'far'} fa-star"></i>
                            ${isFav ? 'Favorilerden Çıkar' : 'Favorilere Ekle'}
                        </button>
                        <a class="github-btn" href="${escHtml(p.github_url)}" target="_blank" rel="noopener">
                            <i class="fab fa-github"></i> GitHub
                        </a>
                    </div>
                </div>`;

            projectContainer.appendChild(card);
        });
    }

    // Delegasyon — tek seferlik üst elemana eklenir
    projectContainer.addEventListener('click', e => {
        const btn = e.target.closest('.view-btn');
        if (!btn) return;
        toggleFavorite(parseInt(btn.dataset.id), btn.dataset.title);
    });

    // Arama
    searchInput?.addEventListener('input', () => {
        activeSearch = searchInput.value.trim();
        if (clearSearchBtn) clearSearchBtn.style.display = activeSearch ? 'block' : 'none';
        render();
    });
    clearSearchBtn?.addEventListener('click', () => {
        searchInput.value = '';
        activeSearch = '';
        clearSearchBtn.style.display = 'none';
        searchInput.focus();
        render();
    });

    // Filtre
    filterChips.forEach(chip => {
        chip.addEventListener('click', () => {
            filterChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeFilter = chip.dataset.filter;
            render();
        });
    });

    // Sıralama
    sortSelect?.addEventListener('change', () => {
        activeSort = sortSelect.value;
        render();
    });

    // Projeleri DB'den çek
    projectContainer.innerHTML = '<div class="empty-state"><i class="fas fa-spinner fa-spin"></i><p>Projeler yükleniyor...</p></div>';

    function loadFallbackProjects() {
        allProjects = [
            { id:1, title:'StreamAtlas', badge:'Web App', image:'streamatlas.png', category:'frontend',
              description:'Film ve dizi keşfetmeye yarayan modern bir streaming platformu arayüzü.', tags:['HTML/CSS','JavaScript','API'], github_url:'https://github.com/OlcayKAHRAMAN2005' },
            { id:2, title:'GSWEB', badge:'ASP.NET', image:'GSWEB_afiş.png', category:'backend',
              description:'Galatasaray taraftar platformu. ASP.NET Core MVC ile geliştirilmiş içerik yönetim sistemi.', tags:['C#','ASP.NET','SQL Server'], github_url:'https://github.com/OlcayKAHRAMAN2005' },
            { id:3, title:'Portfolio Site', badge:'Frontend', image:'frontend.png', category:'frontend',
              description:'Kişisel portfolyo web sitesi. Tema değiştirme ve dinamik içerik yönetimi.', tags:['HTML','CSS','JavaScript'], github_url:'https://github.com/OlcayKAHRAMAN2005' },
            { id:4, title:'Mobile App', badge:'Flutter', image:'mobile.png', category:'mobile',
              description:'Flutter ile geliştirilen cross-platform mobil uygulama.', tags:['Flutter','Dart','Mobile'], github_url:'https://github.com/OlcayKAHRAMAN2005' }
        ];
        render();
    }

    apiFetch('api/projects.php?action=list')
        .then(data => {
            if (data.success && data.projects && data.projects.length > 0) {
                allProjects = data.projects;
                render();
            } else {
                loadFallbackProjects();
            }
        })
        .catch(() => {
            loadFallbackProjects();
        });
}


// =============================================
// İLETİŞİM FORMU — api/contact.php
// =============================================
function initContactForm() {
    const contactForm = document.getElementById('contact-form');
    if (!contactForm) return;

    function escHtmlLocal(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // Form gönder
    contactForm.addEventListener('submit', async e => {
        e.preventDefault();
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const name    = document.getElementById('cf-name')?.value.trim();
        const email   = document.getElementById('cf-email')?.value.trim();
        const subject = document.getElementById('cf-subject')?.value.trim();
        const message = document.getElementById('cf-message')?.value.trim();

        if (!name || !email || !message) {
            window.showToast('⚠️ Ad, e-posta ve mesaj zorunludur.', 'error');
            return;
        }

        if (submitBtn) { submitBtn.disabled = true; submitBtn.style.opacity = '0.7'; }

        try {
            const data = await apiFetch('api/contact.php?action=send', {
                method: 'POST',
                body: JSON.stringify({ name, email, subject, message })
            });

            if (data.success) {
                contactForm.reset();
                window.showToast('✅ ' + data.message);
            } else {
                window.showToast('❌ ' + data.message, 'error');
            }
        } catch {
            window.showToast('❌ Sunucuya bağlanılamadı. PHP çalışıyor mu?', 'error');
        }

        if (submitBtn) { submitBtn.disabled = false; submitBtn.style.opacity = '1'; }
    });
}

// =============================================
// YARDIMCI: HTML ESCAPE
// =============================================
function escHtml(s) {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
