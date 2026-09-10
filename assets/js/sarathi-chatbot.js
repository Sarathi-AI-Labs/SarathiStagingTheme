/**
 * Sarathi AI Labs - Ultra-Premium Next-Gen Intelligent Concierge Chatbot
 * Brand: Sarathi AI Labs | Intelligent IT Solutions & Training
 */

(function () {
  'use strict';

  // Configuration - robust theme URI detection to guarantee identical asset loading on all subpages
  const scriptTag = document.currentScript || document.querySelector('script[src*="sarathi-chatbot"]');
  const detectedThemeUri = (scriptTag && scriptTag.src) ? scriptTag.src.replace(/\/assets\/js\/.*$/, '') : '';

  const themeBase = (window.SARATHI_CHATBOT_SETTINGS && window.SARATHI_CHATBOT_SETTINGS.themeUri)
    || window.SARATHI_THEME_URI
    || detectedThemeUri
    || '/wp-content/themes/custom-theme';

  const CONFIG = {
    themeUri: themeBase,
    avatarUrl: themeBase + '/assets/images/sarathi-avatar.png?v=6',
    apiEndpoint: (window.SARATHI_CHATBOT_SETTINGS && window.SARATHI_CHATBOT_SETTINGS.chatWebhookUrl) || window.SARATHI_CHATBOT_API || 'https://n8n.srv1178467.hstgr.cloud/webhook/sal-ai-chat',
    leadEndpoint: (window.SARATHI_CHATBOT_SETTINGS && window.SARATHI_CHATBOT_SETTINGS.leadWebhookUrl) || window.SARATHI_LEAD_API || 'https://n8n.srv1178467.hstgr.cloud/webhook/sal-lead-cap',
    brandName: 'Sarathi AI Labs',
    brandTagline: 'Intelligent IT Solutions & Training',
    botName: 'Sarathi AI',
    botIconUrl: themeBase + '/assets/images/sarathi-bot-transparent.png?v=5',
    storageKeyHistory: 'sarathi_ai_chat_history',
    storageKeyLead: 'sarathi_ai_lead_info',
    storageKeyPos: 'sarathi_ai_widget_pos',
    storageKeySession: 'sarathi_ai_conv_id',
    storageKeyAudio: 'sarathi_ai_audio_enabled',
    storageKeyOpen: 'sarathi_ai_widget_open'
  };

  // Resilient In-Memory + LocalStorage Fallback (Prevents crashes in Incognito/Blocked Storage)
  const safeStorage = {
    _mem: {},
    getItem(key) {
      try {
        if (typeof window !== 'undefined' && window.localStorage) {
          const val = window.localStorage.getItem(key);
          if (val !== null) return val;
        }
      } catch (e) {}
      return Object.prototype.hasOwnProperty.call(this._mem, key) ? this._mem[key] : null;
    },
    setItem(key, value) {
      const sVal = String(value);
      this._mem[key] = sVal;
      try {
        if (typeof window !== 'undefined' && window.localStorage) {
          window.localStorage.setItem(key, sVal);
        }
      } catch (e) {}
    },
    removeItem(key) {
      delete this._mem[key];
      try {
        if (typeof window !== 'undefined' && window.localStorage) {
          window.localStorage.removeItem(key);
        }
      } catch (e) {}
    }
  };

  // State
  const state = {
    isOpen: safeStorage.getItem('sarathi_ai_widget_open') === 'true',
    theme: 'light',
    audioEnabled: safeStorage.getItem(CONFIG.storageKeyAudio) !== 'false',
    visitorId: getOrCreateVisitorId(),
    conversationId: getOrCreateConversationId(),
    leadInfo: getLeadInfo(),
    history: getChatHistory(),
    isTyping: false
  };

  // Web Audio Synthesizer for Subtle Haptic Feedback (Zero External Dependencies)
  function playHapticTone(type) {
    if (!state.audioEnabled) return;
    try {
      const AudioContext = window.AudioContext || window.webkitAudioContext;
      if (!AudioContext) return;
      const ctx = new AudioContext();
      if (ctx.state === 'suspended') ctx.resume();

      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);

      if (type === 'send') {
        osc.type = 'sine';
        osc.frequency.setValueAtTime(440, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.08);
        gain.gain.setValueAtTime(0.04, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);
        osc.start();
        osc.stop(ctx.currentTime + 0.08);
      } else if (type === 'receive') {
        osc.type = 'sine';
        osc.frequency.setValueAtTime(620, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(920, ctx.currentTime + 0.12);
        gain.gain.setValueAtTime(0.05, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
        osc.start();
        osc.stop(ctx.currentTime + 0.12);
      }
    } catch (e) {}
  }

  // Helper ID generators
  function getOrCreateVisitorId() {
    let vid = safeStorage.getItem('sarathi_ai_visitor_id');
    if (!vid) {
      vid = 'vid_' + Math.random().toString(36).substring(2, 10) + Date.now().toString(36);
      safeStorage.setItem('sarathi_ai_visitor_id', vid);
    }
    return vid;
  }

  function getOrCreateConversationId() {
    let cid = safeStorage.getItem(CONFIG.storageKeySession);
    if (!cid) {
      cid = 'conv_' + Math.random().toString(36).substring(2, 10) + Date.now().toString(36);
      safeStorage.setItem(CONFIG.storageKeySession, cid);
    }
    return cid;
  }

  function getLeadInfo() {
    try {
      return JSON.parse(safeStorage.getItem(CONFIG.storageKeyLead) || '{}');
    } catch (e) {
      return {};
    }
  }

  function getChatHistory() {
    try {
      let history = JSON.parse(safeStorage.getItem(CONFIG.storageKeyHistory) || '[]');
      // If history only contains legacy auto-seeded welcome message(s) without user interaction, treat as empty
      if (history.length > 0 && !history.some(m => m.sender === 'user')) {
        history = [];
        safeStorage.removeItem(CONFIG.storageKeyHistory);
      }
      return history;
    } catch (e) {
      return [];
    }
  }

  function saveChatHistory() {
    try {
      safeStorage.setItem(CONFIG.storageKeyHistory, JSON.stringify(state.history));
    } catch (e) {
      console.error('Failed to save chat history', e);
    }
  }

  // Safe DOM-based High-Performance Markdown & Feature Parser
  function renderMarkdown(text) {
    if (!text) return '';
    
    // Normalize newlines and literal \n from backend JSON
    let raw = text.replace(/\\n/g, '\n').replace(/\r\n/g, '\n');

    // Escape HTML first to prevent XSS
    let html = raw
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');

    // Code blocks with syntax copy button & terminal bar
    html = html.replace(/```([a-zA-Z0-9_-]*)\n([\s\S]*?)```/g, function (match, lang, code) {
      const displayLang = (lang || 'CODE').toUpperCase();
      const codeId = 'code_' + Math.random().toString(36).substring(2, 8);
      return `
        <div class="sarathi-code-container">
          <div class="sarathi-code-header">
            <div class="sarathi-code-dots">
              <span class="dot-red"></span>
              <span class="dot-yellow"></span>
              <span class="dot-green"></span>
            </div>
            <span class="sarathi-code-lang">${displayLang}</span>
            <button class="sarathi-code-copy-btn" data-code-id="${codeId}">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
              </svg>
              <span>Copy</span>
            </button>
          </div>
          <pre><code id="${codeId}">${code.trim()}</code></pre>
        </div>
      `;
    });

    // Inline code
    html = html.replace(/`([^`]+)`/g, '<code class="sarathi-inline-code">$1</code>');

    // Headers with gradient underlines
    html = html.replace(/^### (.*$)/gim, '<h3 class="sarathi-md-h3">$1</h3>');
    html = html.replace(/^## (.*$)/gim, '<h2 class="sarathi-md-h2">$1</h2>');
    html = html.replace(/^# (.*$)/gim, '<h1 class="sarathi-md-h1">$1</h1>');

    // Bold and Italic
    html = html.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
    html = html.replace(/\*([^*]+)\*/g, '<em>$1</em>');
    html = html.replace(/__([^_]+)__/g, '<strong>$1</strong>');
    html = html.replace(/_([^_]+)_/g, '<em>$1</em>');

    // Markdown Tables with sleek glass design
    html = html.replace(/((?:\|.+?\|\n?)+)/g, function (tableMatch) {
      const rows = tableMatch.trim().split('\n');
      if (rows.length < 2) return tableMatch;
      
      let tableHtml = '<div class="sarathi-table-wrap"><table class="sarathi-table">';
      let isHeader = true;

      rows.forEach((row, index) => {
        if (row.match(/^\|?\s*:?-+:?\s*\|/)) {
          isHeader = false;
          return;
        }

        const cols = row.split('|').filter((c, i, arr) => i > 0 && i < arr.length - 1);
        if (cols.length === 0) return;

        tableHtml += '<tr>';
        cols.forEach(col => {
          const tag = index === 0 ? 'th' : 'td';
          tableHtml += `<${tag}>${col.trim()}</${tag}>`;
        });
        tableHtml += '</tr>';
      });

      tableHtml += '</table></div>';
      return tableHtml;
    });

    // Sleek Callout boxes (> [!NOTE] etc.)
    html = html.replace(/^&gt;\s*\[!(NOTE|TIP|IMPORTANT|WARNING|CAUTION)\]\s*(.*)$/gim, function (match, type, content) {
      const typeLower = type.toLowerCase();
      const icons = {
        note: 'ℹ️',
        tip: '💡',
        important: '⚡',
        warning: '⚠️',
        caution: '🛑'
      };
      return `<div class="sarathi-callout sarathi-callout-${typeLower}"><div class="sarathi-callout-icon">${icons[typeLower] || '💡'}</div><div class="sarathi-callout-body"><span class="sarathi-callout-title">${type}</span><p class="sarathi-callout-text">${content.trim()}</p></div></div>`;
    });

    // Helper: Dynamic Contextual Action Button Resolver (Prioritizes exact URL destination from AI Agent)
    function resolveDynamicButtonConfig(rawUrl, linkTitle, fullContext) {
      let href = (rawUrl || '').trim();
      if (/^(javascript|data|vbscript):/i.test(href)) {
        href = '#';
      }
      let isInternal = false;

      // Handle relative paths immediately
      if (href.startsWith('/') || href.startsWith('#') || href.startsWith('./') || href.startsWith('../')) {
        isInternal = true;
      } else {
        // If domain was passed without protocol (e.g. 'sarathiailabs.com/about' or 'staging.sarathiailabs.com')
        if (/^(?:https?:\/\/)/i.test(href) === false && /^(?:[a-zA-Z0-9-]+\.)+(?:com|local|org|net|cloud|io|app)/i.test(href)) {
          href = 'https://' + href;
        }

        try {
          const origin = (typeof window !== 'undefined' && window.location && window.location.origin) ? window.location.origin : 'https://sarathiailabs.com';
          const currentHost = (typeof window !== 'undefined' && window.location && window.location.hostname) ? window.location.hostname.toLowerCase() : 'sarathiailabs.com';
          const configuredSiteUrl = (window.SARATHI_CHATBOT_SETTINGS && window.SARATHI_CHATBOT_SETTINGS.siteUrl) || '';
          let configuredHost = '';
          if (configuredSiteUrl) {
            try { configuredHost = new URL(configuredSiteUrl).hostname.toLowerCase(); } catch (e) {}
          }

          const parsed = new URL(href, origin);
          const targetHost = parsed.hostname.toLowerCase();
          if (
            targetHost === currentHost ||
            (configuredHost && targetHost === configuredHost) ||
            targetHost.includes('sarathiailabs.com') ||
            targetHost.includes('sarathiaiweb.local') ||
            targetHost === 'localhost' ||
            targetHost === '127.0.0.1'
          ) {
            isInternal = true;
            // Always convert to root-relative path so the browser seamlessly stays in the current environment
            href = parsed.pathname + parsed.search + parsed.hash;
          }
        } catch (e) {}
      }

      const normPath = href.toLowerCase();
      const normTitle = (linkTitle || '').toLowerCase();
      const normText = (fullContext || '').toLowerCase();

      const isGenericTitle = (
        !linkTitle ||
        normTitle.includes('learn more') ||
        normTitle.includes('click here') ||
        normTitle === 'more info' ||
        normTitle === 'read more' ||
        normTitle === 'visit' ||
        normTitle === 'link' ||
        normTitle === 'website' ||
        normTitle === 'page'
      );

      // --- PRIORITY 1: Match Exact URL Path Destination ---
      // 1. About Page (/about, /about-us)
      if (normPath.includes('/about') || normPath.includes('about-us') || normPath === 'about') {
        return {
          category: 'about',
          href: isInternal ? (normPath.startsWith('/') ? href : '/about/') : href,
          badge: 'About Sarathi AI',
          title: isGenericTitle ? 'About Sarathi AI Labs' : linkTitle,
          subtitle: 'Who we are, our mission & leadership',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`
        };
      }

      // 2. Contact Page (/contact, /contact-us)
      if (normPath.includes('/contact') || normPath.includes('contact-us') || normPath === 'contact') {
        return {
          category: 'contact',
          href: isInternal ? (normPath.startsWith('/') ? href : '/contact/') : href,
          badge: 'Direct Connect',
          title: isGenericTitle ? 'Contact Us Directly' : linkTitle,
          subtitle: 'Call, email or visit our office',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>`
        };
      }

      // 3. Blog & Articles (/blog, /articles, /news)
      if (normPath.includes('/blog') || normPath.includes('/article') || normPath.includes('/news') || normPath === 'blog') {
        return {
          category: 'blog',
          href: isInternal ? (normPath.startsWith('/') ? href : '/blog/') : href,
          badge: 'AI Insights & Blog',
          title: isGenericTitle ? 'Explore Blog & Insights' : linkTitle,
          subtitle: 'Read latest AI trends & engineering articles',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>`
        };
      }

      // 4. Training & Courses (/training, /courses, /bootcamp)
      if (normPath.includes('/training') || normPath.includes('/course') || normPath.includes('/bootcamp') || normPath === 'training') {
        return {
          category: 'training',
          href: isInternal ? (normPath.startsWith('/') ? href : '/training/') : href,
          badge: 'Professional Training',
          title: isGenericTitle ? 'Explore Training & Courses' : linkTitle,
          subtitle: 'Master Agentic AI & Full-Stack engineering',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>`
        };
      }

      // 5. Solutions & Services (/solutions, /services)
      if (normPath.includes('/solution') || normPath.includes('/service') || normPath.includes('/agentic') || normPath === 'solutions') {
        return {
          category: 'solutions',
          href: isInternal ? (normPath.startsWith('/') ? href : '/solutions/') : href,
          badge: 'AI & Cloud Solutions',
          title: isGenericTitle ? 'Explore AI Solutions & Services' : linkTitle,
          subtitle: 'Autonomous agents, custom RAG & cloud',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>`
        };
      }

      // 6. Careers & Jobs (/careers, /job)
      if (normPath.includes('/career') || normPath.includes('/job') || normPath === 'careers') {
        return {
          category: 'careers',
          href: isInternal ? (normPath.startsWith('/') ? href : '/careers/') : href,
          badge: 'Join Sarathi AI',
          title: isGenericTitle ? 'View Open Roles & Careers' : linkTitle,
          subtitle: 'Grow with our innovative AI engineering team',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>`
        };
      }

      // 7. Consultation & Demo Booking (#consultation, /book-demo)
      if (normPath.includes('consult') || normPath.includes('demo') || normPath.includes('schedule')) {
        return {
          category: 'consultation',
          href: isInternal ? '#consultation' : href,
          badge: 'Complimentary Consultation',
          title: isGenericTitle ? 'Book an AI Architecture Call' : linkTitle,
          subtitle: '30-min strategy & feasibility roadmap',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>`
        };
      }

      // --- PRIORITY 2: Match Link Title Intent ---
      if (normTitle.includes('about')) {
        return {
          category: 'about',
          href: isInternal ? (normPath.startsWith('/') ? href : '/about/') : href,
          badge: 'About Sarathi AI',
          title: isGenericTitle ? 'About Sarathi AI Labs' : linkTitle,
          subtitle: 'Who we are, our mission & leadership',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`
        };
      }
      if (normTitle.includes('contact') || normTitle.includes('reach')) {
        return {
          category: 'contact',
          href: isInternal ? '/contact/' : href,
          badge: 'Direct Connect',
          title: isGenericTitle ? 'Contact Us Directly' : linkTitle,
          subtitle: 'Call, email or visit our office',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>`
        };
      }
      if (normTitle.includes('blog') || normTitle.includes('article') || normTitle.includes('news')) {
        return {
          category: 'blog',
          href: isInternal ? '/blog/' : href,
          badge: 'AI Insights & Blog',
          title: isGenericTitle ? 'Explore Blog & Insights' : linkTitle,
          subtitle: 'Read latest AI trends & engineering articles',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>`
        };
      }
      if (normTitle.includes('training') || normTitle.includes('course') || normTitle.includes('bootcamp')) {
        return {
          category: 'training',
          href: isInternal ? '/training/' : href,
          badge: 'Professional Training',
          title: isGenericTitle ? 'Explore Training & Courses' : linkTitle,
          subtitle: 'Master Agentic AI & Full-Stack engineering',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>`
        };
      }
      if (normTitle.includes('solution') || normTitle.includes('service')) {
        return {
          category: 'solutions',
          href: isInternal ? '/solutions/' : href,
          badge: 'AI & Cloud Solutions',
          title: isGenericTitle ? 'Explore AI Solutions & Services' : linkTitle,
          subtitle: 'Autonomous agents, custom RAG & cloud',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>`
        };
      }

      // --- PRIORITY 3: Context Preceding Link ---
      if (normText.includes('about page') || normText.includes('about us')) {
        return {
          category: 'about',
          href: isInternal ? '/about/' : href,
          badge: 'About Sarathi AI',
          title: isGenericTitle ? 'About Sarathi AI Labs' : linkTitle,
          subtitle: 'Who we are, our mission & leadership',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`
        };
      }
      if (normText.includes('contact page') || normText.includes('reach us') || normText.includes('channels:')) {
        return {
          category: 'contact',
          href: isInternal ? '/contact/' : href,
          badge: 'Direct Connect',
          title: isGenericTitle ? 'Contact Us Directly' : linkTitle,
          subtitle: 'Call, email or visit our office',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>`
        };
      }
      if (normText.includes('our blog') || normText.includes('latest articles')) {
        return {
          category: 'blog',
          href: isInternal ? '/blog/' : href,
          badge: 'AI Insights & Blog',
          title: isGenericTitle ? 'Explore Blog & Insights' : linkTitle,
          subtitle: 'Read latest AI trends & engineering articles',
          isInternal,
          icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>`
        };
      }

      // --- PRIORITY 4: Default Fallback ---
      return {
        category: 'about',
        href: isInternal ? (normPath.startsWith('/') ? href : '/about/') : href,
        badge: 'About Sarathi AI',
        title: isGenericTitle ? 'About Sarathi AI Labs' : linkTitle,
        subtitle: 'Who we are, our mission & leadership',
        isInternal,
        icon: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`
      };
    }

    // Links [title](url) - rendered as Advanced Dynamic Action Cards
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, function (match, title, url) {
      const btn = resolveDynamicButtonConfig(url, title, raw);
      return `\n<div class="sarathi-action-card-wrap"><a href="${btn.href}" class="sarathi-action-btn sarathi-action-${btn.category} ${btn.isInternal ? 'sarathi-internal-link' : ''}" target="${btn.isInternal ? '_self' : '_blank'}" rel="${btn.isInternal ? '' : 'noopener noreferrer'}"><div class="sarathi-action-main"><div class="sarathi-action-icon-box">${btn.icon}</div><div class="sarathi-action-info"><div class="sarathi-action-header-row"><span class="sarathi-action-badge">${btn.badge}</span></div><div class="sarathi-action-title">${btn.title}</div><div class="sarathi-action-subtitle">${btn.subtitle}</div></div></div><div class="sarathi-action-arrow-circle"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></div></a></div>\n`;
    });

    // Standard Blockquote
    html = html.replace(/^&gt;\s*(.*$)/gim, '<blockquote class="sarathi-blockquote"><span class="quote-glyph">“</span><p>$1</p></blockquote>');

    // Line-by-line Clean Minimalist Markdown Formatter
    const lines = html.split('\n');
    let out = [];
    let listBuffer = [];

    function flushList() {
      if (listBuffer.length === 0) return;
      let lHtml = '<ul class="sarathi-ul">';
      listBuffer.forEach(li => {
        lHtml += `<li class="sarathi-li">${li}</li>`;
      });
      lHtml += '</ul>';
      out.push(lHtml);
      listBuffer = [];
    }

    lines.forEach((line) => {
      const trimmed = line.trim();
      if (!trimmed) {
        flushList();
        return;
      }

      // If it's already an HTML block tag (table, code, header, blockquote, action card)
      if (trimmed.startsWith('<div class="sarathi-code') || trimmed.startsWith('<div class="sarathi-table') || trimmed.startsWith('<div class="sarathi-action-card-wrap') || trimmed.startsWith('<h') || trimmed.startsWith('<blockquote')) {
        flushList();
        out.push(trimmed);
        return;
      }

      // Bullet list items
      const bulletMatch = trimmed.match(/^[-*•]\s+(.*)$/);
      if (bulletMatch) {
        listBuffer.push(bulletMatch[1]);
        return;
      }

      // Numbered list items
      const numberMatch = trimmed.match(/^(\d+)\.\s+(.*)$/);
      if (numberMatch) {
        listBuffer.push(`<strong>${numberMatch[1]}.</strong> ${numberMatch[2]}`);
        return;
      }

      flushList();

      // Check if it's a closing question
      const isClosingQuestion = (trimmed.endsWith('?') && (trimmed.startsWith('What') || trimmed.startsWith('How') || trimmed.startsWith('Would you') || trimmed.startsWith('Which') || trimmed.startsWith('Feel free') || trimmed.startsWith('Can I') || trimmed.startsWith('Shall we') || trimmed.startsWith('Is there')));
      if (isClosingQuestion) {
        out.push(`<p class="sarathi-md-question">${trimmed}</p>`);
      } else {
        out.push(`<p class="sarathi-md-p">${trimmed}</p>`);
      }
    });

    flushList();

    return out.join('');
  }

  // Intelligent Sarathi AI Knowledge Engine (Simple, Crisp & Actionable)
  function generateFallbackResponse(query) {
    const q = query.toLowerCase().trim();

    // Affirmative responses (Yes / Sure)
    if (q === 'yes, sure!' || q === 'yes' || q === 'sure' || q.includes('yes,') || q.includes('sure!') || q.includes('schedule') || q.includes('sign me up') || q.includes('claim') || q.includes('yes, let\'s build') || q.includes('book ai consultation')) {
      return {
        category: 'Contact',
        text: `Awesome! Please share your preferred email or phone number, or tell us about your project or training goals below 😊`,
        chips: ['Agentic AI Solutions', 'Web & Cloud Development', 'Professional Training', 'AI Test Automation']
      };
    }

    // Negative responses (No / Thanks)
    if (q === 'no, thanks.' || q === 'no' || q.includes('no, thanks') || q.includes('no thanks') || q === 'not now') {
      return {
        category: 'Assistance',
        text: `No problem at all! Feel free to ask anything about our solutions, technology stack, or courses whenever you're ready 😊`,
        chips: ['Agentic AI Solutions', 'Web & Cloud Development', 'Professional Training', 'AI Test Automation']
      };
    }

    // 1. Agentic AI Solutions
    if (q.includes('agentic') || q.includes('autonomous') || q.includes('rag') || q.includes('swarm') || q.includes('llm') || q === 'agentic ai solutions') {
      return {
        category: 'Agentic AI Solutions',
        text: `At **Sarathi AI Labs**, we architect autonomous AI agent systems and enterprise intelligence solutions:\n\n- **Autonomous Multi-Agent Swarms**: Goal-oriented AI agents orchestrating end-to-end business operations using LangGraph, CrewAI & AutoGen.\n- **Enterprise RAG Knowledge Systems**: Connect private databases and document repositories with hybrid vector search and zero hallucinations.\n- **Custom Tool-Calling & Automation**: Autonomous API execution across CRM, ERP, finance, and internal databases.\n\nWould you like to schedule a free **AI Architecture Consultation** or see a live agent demonstration?`,
        chips: ['Book AI Consultation', 'See Tech Stack', 'Get Custom Quote']
      };
    }

    // 2. Web & Cloud Development
    if (q.includes('web') || q.includes('cloud') || q.includes('fullstack') || q.includes('full stack') || q.includes('frontend') || q.includes('backend') || q.includes('api') || q === 'web & cloud development') {
      return {
        category: 'Web & Cloud Development',
        text: `We engineer fast, scalable web platforms and cloud-native architectures built for high performance:\n\n- **Modern Full-Stack Applications**: High-throughput web applications using Next.js, React, Node.js, Python FastAPI, and headless architectures.\n- **Cloud Infrastructure & Microservices**: Resilient cloud architecture on AWS and GCP with automated Docker & Kubernetes orchestration.\n- **High-Performance APIs & Integrations**: Secure REST/GraphQL gateways, database optimization (PostgreSQL/Redis), and enterprise security.\n\nAre you planning a new web application, modernizing an existing portal, or scaling cloud infrastructure?`,
        chips: ['Build New Web App', 'Get Custom Quote', 'Talk to Tech Lead']
      };
    }

    // 3. Professional Training
    if (q.includes('training') || q.includes('course') || q.includes('bootcamp') || q.includes('learn') || q.includes('program') || q.includes('curriculum') || q.includes('syllabus') || q === 'professional training') {
      return {
        category: 'Professional Training',
        text: `Our industry-grade training programs prepare engineers with hands-on production skills:\n\n- **Agentic AI & LLM Engineering (8 Weeks)**: Multi-agent systems, LangChain, RAG architecture, tool use, and enterprise deployments.\n- **Full-Stack Web Mastery (12 Weeks)**: Next.js, modern backend APIs, cloud deployment, and system architecture.\n- **Enterprise Test Automation (6 Weeks)**: Playwright, Cypress, CI/CD automated test gates, and framework design.\n\nAll programs include **live real-world projects**, **1-on-1 mentor guidance**, and **industry certification**.\n\nWould you like to download the syllabus or schedule a free counseling session?`,
        chips: ['View Syllabus', 'Schedule Free Counseling', 'Talk to Advisor']
      };
    }

    // 4. AI Test Automation
    if (q.includes('test') || q.includes('automation') || q.includes('qa') || q.includes('playwright') || q.includes('selenium') || q === 'ai test automation') {
      return {
        category: 'AI Test Automation',
        text: `We deliver intelligent test automation frameworks and AI-assisted quality engineering to guarantee zero-bug releases:\n\n- **End-to-End Test Automation**: Robust Playwright and Cypress test suites running across desktop and mobile browsers.\n- **AI-Powered Visual & Regression Testing**: Intelligent visual change detection and self-healing test locators.\n- **CI/CD Quality Gates**: Automated continuous testing integrated with GitHub Actions, GitLab CI, and Docker pipelines.\n- **API & Load Performance**: Comprehensive Postman/Newman and k6 load simulation suites.\n\nWould you like a free **QA Framework Audit** for your application or to discuss custom test automation?`,
        chips: ['Get Free QA Audit', 'Tools We Support', 'Talk to QA Lead']
      };
    }

    // Contact / Human Advisor
    if (q.includes('advisor') || q.includes('contact') || q.includes('talk') || q.includes('human') || q.includes('phone') || q.includes('email') || q.includes('call')) {
      return {
        category: 'Contact',
        text: `Our technical advisory team is ready to assist you. You can reach us directly at **contact@sarathiai.com** or book a consultation.\n\nWould you like us to schedule a call with a specialist?`,
        chips: ['Yes, sure!', 'Send Email', 'No, thanks.']
      };
    }

    // What services do you offer?
    if (q.includes('what services') || q.includes('services do you offer') || q.includes('services and solutions')) {
      return {
        category: 'Our Services',
        text: `At **Sarathi AI Labs**, we provide cutting-edge technology and talent solutions:\n\n1. 🤖 **Agentic AI Solutions**: Autonomous multi-agent swarms (LangGraph/CrewAI), custom RAG enterprise knowledge hubs, and intelligent tool-calling.\n2. 💻 **Web & Cloud Development**: High-performance full-stack web applications, cloud microservices on AWS/GCP, and secure REST/GraphQL APIs.\n3. 🎓 **Professional Training**: Industry-ready bootcamps in Agentic AI, Full-Stack Engineering, and Test Automation.\n4. ⚡ **AI Test Automation**: Robust Playwright/Cypress end-to-end automation, CI/CD quality gates, and visual testing.\n\nWhich service would you like to explore further?`
      };
    }

    // How can AI help my business?
    if (q.includes('how can ai help') || q.includes('ai help my business') || q.includes('help my business')) {
      return {
        category: 'Business Impact',
        text: `AI empowers your business to achieve unprecedented operational velocity and cost efficiency:\n\n- ⚡ **10x Operational Speed**: Autonomous agents execute repetitive cross-platform workflows, customer support, and data extraction 24/7.\n- 🧠 **Instant Private Intelligence**: Enterprise RAG turns internal documents, SOPs, and databases into an interactive AI expert with zero hallucinations.\n- 📉 **Reduced Operational Costs**: Automate triage, CRM data entry, and QA testing cycles while maintaining 99.9% accuracy.\n\nWould you like a free consultation to identify the high-ROI AI opportunities for your business?`
      };
    }

    // Do you provide custom solutions?
    if (q.includes('custom solutions') || q.includes('custom solution') || q.includes('bespoke')) {
      return {
        category: 'Custom Solutions',
        text: `Yes, absolutely! Every enterprise has unique infrastructure and requirements. We build **100% custom, tailor-made solutions**:\n\n- Custom AI agent swarms integrated directly with your private APIs, ERP, and databases.\n- Tailored web platforms engineered from ground up using your chosen tech stack (Next.js, Python, Node.js, Go).\n- Dedicated on-premise or private cloud LLM deployments for strict data privacy and compliance.\n\nTell us about your project or use case, and our engineering team will propose a custom architectural roadmap.`
      };
    }

    // How can I get started?
    if (q.includes('get started') || q.includes('how can i start') || q.includes('how to start')) {
      return {
        category: 'Getting Started',
        text: `Getting started with **Sarathi AI Labs** is simple and frictionless:\n\n1. 💬 **Share Your Goal**: Tell us about your project, software needs, or team training goals right here in the chat.\n2. 📅 **Discovery Session**: We'll schedule a complimentary 30-minute technical architecture or counseling call.\n3. 🚀 **Prototype & Delivery**: We deliver a crisp milestone roadmap, working POC, and production-ready implementation.\n\nWould you like to request a quote or connect with our lead architect?`
      };
    }

    // Pricing / Quote
    if (q.includes('pricing') || q.includes('cost') || q.includes('quote') || q.includes('fee') || q === 'get a quote') {
      return {
        category: 'Get a Quote',
        text: `We would love to discuss your project and provide a tailored scope and quote! 💼\n\nPlease share:\n- A brief summary of your project or requirements\n- Your target timeline or budget (optional)\n- Your preferred contact email or phone number\n\nOur solutions team will analyze your needs and get back to you with a comprehensive proposal within 24 hours.`
      };
    }

    // Default friendly greeting
    return {
      category: 'Sarathi Concierge',
      text: `Hi there! I can empower you with **Agentic AI Solutions**, **Web & Cloud Development**, **Professional Training**, or **AI Test Automation**.\n\nWhich area would you like to explore?`,
      chips: ['Agentic AI Solutions', 'Web & Cloud Development', 'Professional Training', 'AI Test Automation']
    };
  }

  // Dynamic Contextual Recommended Questions Generator
  function getRecommendedQuestions(userQuery = '', aiResponseText = '') {
    const q = (userQuery + ' ' + aiResponseText).toLowerCase();

    const recommendationPools = {
      agentic: [
        'How do multi-agent swarms communicate?',
        'Can we connect private enterprise databases?',
        'What LLM models and frameworks do you use?',
        'How does custom tool-calling work?',
        'Book a free AI architecture consultation'
      ],
      web: [
        'What frontend & backend frameworks do you use?',
        'Can you help migrate our infrastructure to AWS/GCP?',
        'How do you optimize API latency & caching?',
        'What is your typical web project timeline?',
        'Request a custom project estimate'
      ],
      training: [
        'Can I see the full syllabus & curriculum?',
        'Are training sessions live with mentors?',
        'Do you provide hands-on project portfolio reviews?',
        'What are the prerequisites to enroll?',
        'Schedule a free 1-on-1 counseling call'
      ],
      test: [
        'Do you support Playwright and GitHub Actions CI/CD?',
        'Can you automate our existing manual test cases?',
        'How do AI self-healing test locators work?',
        'Do you perform load and API performance testing?',
        'Request a free QA framework audit'
      ],
      contact: [
        'How soon can we schedule a technical consultation?',
        'Can we set up a Google Meet or Zoom call?',
        'What is your team contact email and phone?',
        'Talk to a solutions architect'
      ],
      general: [
        'What is your typical project delivery timeline?',
        'Can you share case studies or demos of past work?',
        'How do we get started with a new project?',
        'Do you offer custom enterprise solutions?',
        'Can I speak with a technical advisor?'
      ]
    };

    let pool = recommendationPools.general;
    if (q.includes('agentic') || q.includes('autonomous') || q.includes('rag') || q.includes('swarm') || q.includes('llm')) {
      pool = recommendationPools.agentic;
    } else if (q.includes('web') || q.includes('cloud') || q.includes('fullstack') || q.includes('frontend') || q.includes('backend') || q.includes('api') || q.includes('devops')) {
      pool = recommendationPools.web;
    } else if (q.includes('training') || q.includes('course') || q.includes('bootcamp') || q.includes('learn') || q.includes('syllabus') || q.includes('enroll')) {
      pool = recommendationPools.training;
    } else if (q.includes('test') || q.includes('automation') || q.includes('qa') || q.includes('playwright') || q.includes('selenium') || q.includes('bug')) {
      pool = recommendationPools.test;
    } else if (q.includes('contact') || q.includes('talk') || q.includes('call') || q.includes('email') || q.includes('phone') || q.includes('advisor') || q.includes('meet')) {
      pool = recommendationPools.contact;
    }

    // Shuffle and return 3 distinct recommendations
    const shuffled = [...pool].sort(() => 0.5 - Math.random());
    return shuffled.slice(0, 3);
  }

  // Helper to determine if lead details (Name, Email, Phone) are complete
  function isLeadComplete(info) {
    return Boolean(info && info.name && info.email && info.phone);
  }

  // Build Floating UI DOM with Scoped High-Tech Design
  function createWidgetDOM() {
    if (document.getElementById('sarathi-ai-root')) return;
    const root = document.createElement('div');
    root.id = 'sarathi-ai-root';
    root.setAttribute('data-theme', state.theme);

    root.innerHTML = `
      <!-- Launcher Mascot with Hello! Bubble -->
      <div class="sarathi-launcher-container" id="sarathi-launcher-wrap">
        <button class="sarathi-launcher-btn" id="sarathi-launcher-btn" aria-label="Open Sarathi AI Chatbot">
          <img src="${CONFIG.botIconUrl}" alt="Sarathi AI" class="sarathi-launcher-mascot-img" onerror="if(this.src.indexOf('/wp-content/')===-1){this.src='/wp-content/themes/custom-theme/assets/images/sarathi-bot-transparent.png?v=5';}else{this.onerror=null; this.src='data:image/svg+xml;utf8,<svg viewBox=\\'0 0 100 100\\' fill=\\'none\\' xmlns=\\'http://www.w3.org/2000/svg\\'><circle cx=\\'50\\' cy=\\'50\\' r=\\'46\\' fill=\\'%232563EB\\'/><circle cx=\\'50\\' cy=\\'22\\' r=\\'5\\' fill=\\'%2338BDF8\\'/><rect x=\\'47.5\\' y=\\'26\\' width=\\'5\\' height=\\'6\\' rx=\\'2.5\\' fill=\\'%23FFFFFF\\'/><rect x=\\'23\\' y=\\'32\\' width=\\'54\\' height=\\'46\\' rx=\\'18\\' fill=\\'%23FFFFFF\\'/><rect x=\\'29\\' y=\\'38\\' width=\\'42\\' height=\\'34\\' rx=\\'12\\' fill=\\'%230F172A\\'/><circle cx=\\'41.5\\' cy=\\'52\\' r=\\'4.5\\' fill=\\'%2338BDF8\\'/><circle cx=\\'58.5\\' cy=\\'52\\' r=\\'4.5\\' fill=\\'%2338BDF8\\'/><path d=\\'M 44.5 59.5 Q 50 64 55.5 59.5\\' stroke=\\'%2338BDF8\\' stroke-width=\\'2.5\\' stroke-linecap=\\'round\\' fill=\\'none\\'/></svg>';}" />
          <span class="sarathi-launcher-icon-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </span>
        </button>
      </div>

      <!-- Chat Modal Window -->
      <div class="sarathi-chat-window" id="sarathi-chat-window" data-theme="${state.theme}" role="dialog" aria-modal="true">
        
        <!-- Header (Vibrant Blue Gradient with Curved Wave Divider matching Reference UI) -->
        <div class="sarathi-chat-header" id="sarathi-header">
          <div class="sarathi-chat-header-main">
            <!-- Header Info: Avatar + Title & Status on left, Actions on right -->
            <div class="sarathi-chat-header-top">
              <div class="sarathi-chat-header-info">
                <div class="sarathi-avatar-wrap">
                  <div class="sarathi-avatar-inner">
                    <img src="${CONFIG.avatarUrl}" alt="Sarathi AI" class="sarathi-header-avatar-img" onerror="this.onerror=null; this.src='${CONFIG.botIconUrl}';" />
                  </div>
                </div>
                <div class="sarathi-chat-header-title-wrap">
                  <h3 class="sarathi-chat-header-name">Chat with Sarathi</h3>
                  <div class="sarathi-chat-header-status-row">
                    <span class="sarathi-live-dot"></span>
                    <span class="sarathi-status-label">Online</span>
                  </div>
                </div>
              </div>
              
              <div class="sarathi-chat-header-actions">
                <!-- Fullscreen Toggle Button -->
                <button class="sarathi-icon-btn sarathi-btn-fullscreen" id="sarathi-btn-fullscreen" title="Full Screen" aria-label="Toggle Fullscreen">
                  <svg class="sarathi-icon-expand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                  </svg>
                  <svg class="sarathi-icon-compress" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                    <path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/>
                  </svg>
                </button>

                <!-- Options Menu Button (3 Dots) -->
                <div class="sarathi-dropdown-wrap">
                  <button class="sarathi-icon-btn sarathi-btn-settings" id="sarathi-btn-settings" title="Options" aria-label="Chat Options">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                      <circle cx="12" cy="5" r="2"/>
                      <circle cx="12" cy="12" r="2"/>
                      <circle cx="12" cy="19" r="2"/>
                    </svg>
                  </button>
                  <div class="sarathi-settings-dropdown" id="sarathi-settings-menu">
                    <button type="button" class="sarathi-menu-item" id="sarathi-menu-fullscreen">
                      <span class="sarathi-menu-icon">⛶</span>
                      <span id="sarathi-menu-fullscreen-label">Full Screen Mode</span>
                    </button>
                    <button type="button" class="sarathi-menu-item" id="sarathi-menu-audio">
                      <span class="sarathi-menu-icon" id="sarathi-menu-audio-icon">${state.audioEnabled ? '🔊' : '🔇'}</span>
                      <span id="sarathi-menu-audio-label">Sound Feedback</span>
                    </button>
                    <div class="sarathi-menu-divider"></div>
                    <button type="button" class="sarathi-menu-item sarathi-menu-item-danger" id="sarathi-menu-reset">
                      <span class="sarathi-menu-icon">🔄</span>
                      <span>Clear Conversation</span>
                    </button>
                  </div>
                </div>

                <!-- Minimize Chevron Button -->
                <button class="sarathi-icon-btn sarathi-btn-minimize" id="sarathi-btn-minimize" title="Minimize" aria-label="Minimize Chat">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Bottom Dynamic S-Wave Ribbon Divider matching User Reference -->
          <div class="sarathi-header-wave" aria-hidden="true">
            <svg viewBox="0 0 600 48" fill="none" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Sky Blue / Cyan Accent Ribbon Band -->
              <path d="M0,14 C140,44 220,42 340,18 C430,0 520,4 600,34 L600,48 L0,48 Z" fill="#38BDF8" opacity="0.95"/>
              <!-- Crisp White Contour Separator Line -->
              <path d="M0,12 C140,42 220,40 340,16 C430,-2 520,2 600,32" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round"/>
              <!-- Bottom Pure White Body Fill -->
              <path d="M0,22 C140,52 220,50 340,26 C430,8 520,12 600,42 L600,48 L0,48 Z" fill="#FFFFFF"/>
            </svg>
          </div>
        </div>

        <!-- Body / Screens Container -->
        <div class="sarathi-chat-body" id="sarathi-body">
          
          <!-- Screen 1: Mandatory Gatekeeper Screen -->
          <div class="sarathi-gate-view" id="sarathi-gate-view" style="${isLeadComplete(state.leadInfo) ? 'display: none;' : 'display: flex;'}">
            
            <div class="sarathi-gate-greeting-icon">👋</div>

            <div class="sarathi-gate-content">
              <h3 class="sarathi-gate-title">Welcome to Sarathi AI</h3>
              <p class="sarathi-gate-subtitle">Please enter your details to start chatting.</p>
            </div>
            
            <div class="sarathi-gate-form">
              <div class="sarathi-gate-input-group">
                <input type="text" id="sarathi-gate-name" class="sarathi-gate-input" placeholder="Your Name" required autocomplete="name">
              </div>

              <div class="sarathi-gate-input-group">
                <input type="email" id="sarathi-gate-email" class="sarathi-gate-input" placeholder="Work Email" required autocomplete="email">
              </div>

              <div class="sarathi-gate-input-group">
                <input type="tel" id="sarathi-gate-phone" class="sarathi-gate-input" placeholder="Phone Number" required autocomplete="tel">
              </div>

              <div class="sarathi-gate-error" id="sarathi-gate-error">
                <span>Please fill in Name, Email, and Phone Number to continue.</span>
              </div>

              <button class="sarathi-gate-btn" id="sarathi-gate-submit">
                <span>Start Chat</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="5" y1="12" x2="19" y2="12"/>
                  <polyline points="12 5 19 12 12 19"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Screen 2: Clean Minimalist Welcome Hub matching Reference UI -->
          <div class="sarathi-welcome-screen" id="sarathi-welcome-screen" style="${isLeadComplete(state.leadInfo) && (!state.history || !state.history.some(m => m.sender === 'user')) ? 'display: flex;' : 'display: none;'}">

            <!-- Chatbot Greeting Bubble -->
            <div class="sarathi-welcome-bubble">
              <div class="sarathi-welcome-msg-text">
                <p id="sarathi-welcome-text"><span class="sarathi-welcome-greeting">Hi ${state.leadInfo && state.leadInfo.name ? `<strong class="sarathi-user-firstname">${state.leadInfo.name.split(' ')[0]}</strong>` : 'there'} 👋</span><span class="sarathi-welcome-question">How can we empower you with intelligent technology today?</span></p>
                <div class="sarathi-welcome-action-wrap">
                  <a href="/about" target="_self" class="sarathi-learn-more-btn sarathi-internal-link" id="sarathi-learn-more-btn" title="Learn more about Sarathi AI Labs">
                    <span class="sarathi-btn-spark">✦</span>
                    <span class="sarathi-learn-more-text">Learn more about Sarathi AI Labs</span>
                    <span class="sarathi-learn-more-arrow">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                      </svg>
                    </span>
                  </a>
                </div>
              </div>
            </div>



            <!-- Clean Topic Bento Cards Grid (2x2) -->
            <div class="sarathi-topic-grid">
              
              <div class="sarathi-topic-card" data-topic="Agentic AI Solutions" style="--card-delay: 0.04s;">
                <div class="sarathi-topic-icon-badge badge-blue">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="8" width="16" height="12" rx="4"></rect>
                    <circle cx="9" cy="13" r="1.5" fill="#0284C7"></circle>
                    <circle cx="15" cy="13" r="1.5" fill="#0284C7"></circle>
                    <path d="M9 17h6"></path>
                    <line x1="12" y1="4" x2="12" y2="8"></line>
                    <circle cx="12" cy="3.5" r="1" fill="#0284C7"></circle>
                    <line x1="1.5" y1="14" x2="4" y2="14"></line>
                    <line x1="20" y1="14" x2="22.5" y2="14"></line>
                  </svg>
                </div>
                <div class="sarathi-topic-info">
                  <h4>Agentic AI Solutions</h4>
                  <p>Autonomous AI agents & enterprise RAG</p>
                </div>
                <span class="sarathi-topic-arrow">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </span>
              </div>

              <div class="sarathi-topic-card" data-topic="Web & Cloud Development" style="--card-delay: 0.08s;">
                <div class="sarathi-topic-icon-badge badge-green">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2"></rect>
                    <line x1="2" y1="20" x2="22" y2="20"></line>
                  </svg>
                </div>
                <div class="sarathi-topic-info">
                  <h4>Web & Cloud Development</h4>
                  <p>Modern web apps & cloud solutions</p>
                </div>
                <span class="sarathi-topic-arrow">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </span>
              </div>

              <div class="sarathi-topic-card" data-topic="Professional Training" style="--card-delay: 0.12s;">
                <div class="sarathi-topic-icon-badge badge-purple">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9333EA" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c0 3 4 5 6 5s6-2 6-5v-5"></path>
                  </svg>
                </div>
                <div class="sarathi-topic-info">
                  <h4>Professional Training</h4>
                  <p>AI & technology training for teams and individuals</p>
                </div>
                <span class="sarathi-topic-arrow">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </span>
              </div>

              <div class="sarathi-topic-card" data-topic="Get a Quote" style="--card-delay: 0.16s;">
                <div class="sarathi-topic-icon-badge badge-orange">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    <line x1="8" y1="11" x2="8.01" y2="11" stroke-width="2.5"></line>
                    <line x1="12" y1="11" x2="12.01" y2="11" stroke-width="2.5"></line>
                    <line x1="16" y1="11" x2="16.01" y2="11" stroke-width="2.5"></line>
                  </svg>
                </div>
                <div class="sarathi-topic-info">
                  <h4>Get a Quote</h4>
                  <p>Discuss your project with our experts</p>
                </div>
                <span class="sarathi-topic-arrow">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </span>
              </div>

            </div>

            <!-- Popular Questions Section matching Reference UI -->
            <div class="sarathi-popular-section">
              <div class="sarathi-popular-header">
                <h4 class="sarathi-popular-title">Popular questions</h4>
                <button type="button" class="sarathi-popular-see-all">See all &gt;</button>
              </div>

              <div class="sarathi-popular-grid">
                <button type="button" class="sarathi-popular-q-btn" data-question="What services do you offer?">
                  <div class="sarathi-pop-q-icon pop-icon-blue">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                      <polyline points="14 2 14 8 20 8"></polyline>
                      <line x1="16" y1="13" x2="8" y2="13"></line>
                      <line x1="16" y1="17" x2="8" y2="17"></line>
                      <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                  </div>
                  <span>What services do you offer?</span>
                </button>

                <button type="button" class="sarathi-popular-q-btn" data-question="How can AI help my business?">
                  <div class="sarathi-pop-q-icon pop-icon-blue">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0284C7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                  </div>
                  <span>How can AI help my business?</span>
                </button>

                <button type="button" class="sarathi-popular-q-btn" data-question="Do you provide custom solutions?">
                  <div class="sarathi-pop-q-icon pop-icon-green">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="3"></circle>
                      <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                  </div>
                  <span>Do you provide custom solutions?</span>
                </button>

                <button type="button" class="sarathi-popular-q-btn" data-question="How can I get started?">
                  <div class="sarathi-pop-q-icon pop-icon-orange">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#EA580C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                  </div>
                  <span>How can I get started?</span>
                </button>
              </div>
            </div>

          </div>

          <!-- Screen 3: Messages Stream Container -->
          <div class="sarathi-chat-messages" id="sarathi-messages-list" style="${isLeadComplete(state.leadInfo) && state.history && state.history.some(m => m.sender === 'user') ? 'display: flex;' : 'display: none;'}">
            <!-- Messages injected dynamically -->
          </div>

          <!-- Dynamic Streaming / Advanced AI Thinking Indicator -->
          <div id="sarathi-typing-wrap" class="sarathi-typing-wrapper" style="display: none;">
            <div class="sarathi-thinking-card">
              <!-- Top Row: Avatar with glowing ring, title, live stopwatch, and active stage text -->
              <div class="sarathi-thinking-header">
                <div class="sarathi-thinking-avatar-box">
                  <div class="sarathi-thinking-avatar-ring"></div>
                  <div class="sarathi-thinking-avatar-inner">
                    <img src="${CONFIG.avatarUrl}" alt="Sarathi AI" class="sarathi-thinking-avatar-img" />
                  </div>
                </div>
                <div class="sarathi-thinking-meta">
                  <div class="sarathi-thinking-title-row">
                    <span class="sarathi-thinking-sparkle">✦</span>
                    <span class="sarathi-thinking-title">Sarathi AI is thinking</span>
                    <span class="sarathi-thinking-timer-pill" id="sarathi-thinking-timer">0.0s</span>
                  </div>
                  <div class="sarathi-thinking-status-row">
                    <span class="sarathi-thinking-status-dot"></span>
                    <span class="sarathi-thinking-step-text" id="sarathi-thinking-step-text">Analyzing query and context...</span>
                  </div>
                </div>
              </div>

              <!-- Pipeline: Real-time Multi-Stage Progress Tracker -->
              <div class="sarathi-thinking-pipeline">
                <div class="sarathi-thinking-stage active" id="sarathi-stage-1">
                  <span class="stage-icon">🧠</span>
                  <span class="stage-label">Intent</span>
                </div>
                <div class="sarathi-thinking-connector" id="sarathi-connector-1"></div>
                <div class="sarathi-thinking-stage" id="sarathi-stage-2">
                  <span class="stage-icon">🔍</span>
                  <span class="stage-label">Knowledge</span>
                </div>
                <div class="sarathi-thinking-connector" id="sarathi-connector-2"></div>
                <div class="sarathi-thinking-stage" id="sarathi-stage-3">
                  <span class="stage-icon">⚡</span>
                  <span class="stage-label">Reasoning</span>
                </div>
                <div class="sarathi-thinking-connector" id="sarathi-connector-3"></div>
                <div class="sarathi-thinking-stage" id="sarathi-stage-4">
                  <span class="stage-icon">✨</span>
                  <span class="stage-label">Synthesis</span>
                </div>
              </div>

              <!-- Smooth Neural Shimmer Track -->
              <div class="sarathi-thinking-neural-track">
                <div class="sarathi-thinking-neural-pulse"></div>
              </div>
            </div>
          </div>

        </div>

        <!-- Composer / Input Container matching Reference UI -->
        <div class="sarathi-composer-container" id="sarathi-composer-container" style="${isLeadComplete(state.leadInfo) ? 'display: flex;' : 'display: none;'}">
          <div class="sarathi-composer-box">
            <textarea id="sarathi-input" class="sarathi-textarea" placeholder="Enter your message..." rows="1"></textarea>
            
            <div class="sarathi-composer-actions">
              <div class="sarathi-composer-tools">
                <!-- Emoji Selector Wrap -->
                <div class="sarathi-emoji-picker-wrap">
                  <button type="button" class="sarathi-tool-btn" id="sarathi-btn-emoji" title="Insert Emoji" aria-label="Emoji">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="10"></circle>
                      <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                      <line x1="9" y1="9" x2="9.01" y2="9"></line>
                      <line x1="15" y1="9" x2="15.01" y2="9"></line>
                    </svg>
                  </button>
                  <div class="sarathi-emoji-popup" id="sarathi-emoji-popup">
                    <div class="sarathi-emoji-grid">
                      <button type="button" class="sarathi-emoji-item" data-emoji="😊">😊</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="👋">👋</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="🤖">🤖</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="🚀">🚀</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="⚡">⚡</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="💡">💡</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="👍">👍</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="❤️">❤️</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="💻">💻</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="💼">💼</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="🔥">🔥</button>
                      <button type="button" class="sarathi-emoji-item" data-emoji="🎯">🎯</button>
                    </div>
                  </div>
                </div>

                <!-- Attachment Button -->
                <button type="button" class="sarathi-tool-btn" id="sarathi-btn-attach" title="Attach Document or Inquire" aria-label="Attachments">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path>
                  </svg>
                </button>
                <input type="file" id="sarathi-file-input" style="display: none;" accept="image/*,.pdf,.doc,.docx,.txt">
              </div>

              <!-- Send Button (Prominent Navy Circle matching Reference UI) -->
              <button class="sarathi-send-btn" id="sarathi-btn-send" title="Send Message" aria-label="Send message">
                <svg viewBox="0 0 24 24" fill="currentColor">
                  <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
              </button>
            </div>
          </div>
          <div class="sarathi-footer-notice">
            <span class="footer-badge">✦ Enterprise RAG</span>
            <span class="footer-sep">•</span>
            <span>Powered by <a href="/" target="_blank">Sarathi AI Labs</a></span>
          </div>
        </div>

      </div>

      <!-- Toast Feedback Notification -->
      <div id="sarathi-toast" class="sarathi-toast"></div>
    `;

    document.body.appendChild(root);
  }

  // Toast Notification Helper
  function showToast(message) {
    const toast = document.getElementById('sarathi-toast');
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('visible');
    setTimeout(() => toast.classList.remove('visible'), 2400);
  }

  // Setup Event Listeners & Interaction Engine
  function initChatbot() {
    createWidgetDOM();

    // DOM Elements
    const launcherWrap = document.getElementById('sarathi-launcher-wrap');
    const launcherBtn = document.getElementById('sarathi-launcher-btn');
    const launcherPill = document.getElementById('sarathi-launcher-pill');
    const chatWindow = document.getElementById('sarathi-chat-window');
    const header = document.getElementById('sarathi-header');
    const btnClose = document.getElementById('sarathi-btn-close');
    const btnMinimize = document.getElementById('sarathi-btn-minimize');
    const btnFullscreen = document.getElementById('sarathi-btn-fullscreen');
    const btnSettings = document.getElementById('sarathi-btn-settings');
    const settingsMenu = document.getElementById('sarathi-settings-menu');
    const menuFullscreen = document.getElementById('sarathi-menu-fullscreen');
    const menuFullscreenLabel = document.getElementById('sarathi-menu-fullscreen-label');
    const menuAudio = document.getElementById('sarathi-menu-audio');
    const menuReset = document.getElementById('sarathi-menu-reset');
    const welcomeScreen = document.getElementById('sarathi-welcome-screen');
    const messagesList = document.getElementById('sarathi-messages-list');
    const typingWrap = document.getElementById('sarathi-typing-wrap');
    const textarea = document.getElementById('sarathi-input');
    const btnSend = document.getElementById('sarathi-btn-send');
    const btnEmoji = document.getElementById('sarathi-btn-emoji');
    const emojiPopup = document.getElementById('sarathi-emoji-popup');
    const btnAttach = document.getElementById('sarathi-btn-attach');
    const fileInput = document.getElementById('sarathi-file-input');
    const gateView = document.getElementById('sarathi-gate-view');
    const gateName = document.getElementById('sarathi-gate-name');
    const gateEmail = document.getElementById('sarathi-gate-email');
    const gatePhone = document.getElementById('sarathi-gate-phone');
    const gateError = document.getElementById('sarathi-gate-error');
    const gateSubmit = document.getElementById('sarathi-gate-submit');
    const composerContainer = document.getElementById('sarathi-composer-container');
    let currentEditingMsgId = null;

    // In-Page Fullscreen Toggle
    function toggleFullscreen(force) {
      if (!chatWindow) return;
      const isFull = typeof force === 'boolean' ? force : !chatWindow.classList.contains('sarathi-fullscreen-mode');
      const iconExpand = chatWindow.querySelector('.sarathi-icon-expand');
      const iconCompress = chatWindow.querySelector('.sarathi-icon-compress');

      if (isFull) {
        chatWindow.classList.add('sarathi-fullscreen-mode');
        document.body.classList.add('sarathi-fullscreen-active');
        if (iconExpand) iconExpand.style.display = 'none';
        if (iconCompress) iconCompress.style.display = 'block';
        if (menuFullscreenLabel) menuFullscreenLabel.textContent = 'Exit Full Screen';
        if (btnFullscreen) btnFullscreen.setAttribute('title', 'Exit Full Screen');
        showToast('Full screen mode active ⛶');
      } else {
        chatWindow.classList.remove('sarathi-fullscreen-mode');
        document.body.classList.remove('sarathi-fullscreen-active');
        if (iconExpand) iconExpand.style.display = 'block';
        if (iconCompress) iconCompress.style.display = 'none';
        if (menuFullscreenLabel) menuFullscreenLabel.textContent = 'Full Screen Mode';
        if (btnFullscreen) btnFullscreen.setAttribute('title', 'Full Screen');

        // Restore position
        const savedPos = safeStorage.getItem(CONFIG.storageKeyPos);
        if (savedPos) {
          try {
            const p = JSON.parse(savedPos);
            if (typeof p.right === 'number') chatWindow.style.right = p.right + 'px';
            if (typeof p.bottom === 'number') chatWindow.style.bottom = p.bottom + 'px';
          } catch (e) {}
        }
      }
      scrollToLatestExchange(false);
    }

    if (btnFullscreen) {
      btnFullscreen.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleFullscreen();
      });
      btnFullscreen.addEventListener('pointerdown', (e) => e.stopPropagation());
    }

    // Settings Dropdown Toggle
    if (btnSettings && settingsMenu) {
      btnSettings.addEventListener('click', (e) => {
        e.stopPropagation();
        settingsMenu.classList.toggle('active');
      });
      btnSettings.addEventListener('pointerdown', (e) => e.stopPropagation());
      document.addEventListener('click', (e) => {
        if (!btnSettings.contains(e.target) && !settingsMenu.contains(e.target)) {
          settingsMenu.classList.remove('active');
        }
      });
    }

    if (menuFullscreen) {
      menuFullscreen.addEventListener('click', (e) => {
        e.stopPropagation();
        if (settingsMenu) settingsMenu.classList.remove('active');
        toggleFullscreen();
      });
    }

    if (menuAudio) {
      menuAudio.addEventListener('click', (e) => {
        e.stopPropagation();
        state.audioEnabled = !state.audioEnabled;
        safeStorage.setItem(CONFIG.storageKeyAudio, state.audioEnabled);
        const audioIcon = document.getElementById('sarathi-menu-audio-icon');
        if (audioIcon) audioIcon.textContent = state.audioEnabled ? '🔊' : '🔇';
        showToast(state.audioEnabled ? 'Sound feedback on 🔊' : 'Sound feedback muted 🔇');
        if (settingsMenu) settingsMenu.classList.remove('active');
      });
    }

    // Clear Conversation Action
    if (menuReset) {
      menuReset.addEventListener('click', (e) => {
        e.stopPropagation();
        if (settingsMenu) settingsMenu.classList.remove('active');
        currentEditingMsgId = null;
        state.history = [];
        saveChatHistory();
        renderChatHistory();
        showToast('Conversation cleared ✨');
      });
    }

    // Keyboard ESC exits full screen mode
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && chatWindow && chatWindow.classList.contains('sarathi-fullscreen-mode')) {
        toggleFullscreen(false);
      }
    });

    // Emoji Picker Toggle & Selection
    if (btnEmoji && emojiPopup) {
      btnEmoji.addEventListener('click', (e) => {
        e.stopPropagation();
        emojiPopup.classList.toggle('active');
      });
      document.addEventListener('click', (e) => {
        if (!btnEmoji.contains(e.target) && !emojiPopup.contains(e.target)) {
          emojiPopup.classList.remove('active');
        }
      });
      emojiPopup.querySelectorAll('.sarathi-emoji-item').forEach(item => {
        item.addEventListener('click', (e) => {
          e.stopPropagation();
          const emoji = item.getAttribute('data-emoji');
          if (textarea && emoji) {
            textarea.value += emoji;
            textarea.focus();
            textarea.dispatchEvent(new Event('input'));
          }
          emojiPopup.classList.remove('active');
        });
      });
    }

    // Attachment Button & File Upload
    if (btnAttach && fileInput) {
      btnAttach.addEventListener('click', (e) => {
        e.stopPropagation();
        fileInput.click();
      });
      fileInput.addEventListener('change', () => {
        if (fileInput.files && fileInput.files[0]) {
          const file = fileInput.files[0];
          showToast(`Attached: ${file.name} 📎`);
          if (textarea) {
            textarea.value += (textarea.value ? ' ' : '') + `[Attached: ${file.name}] `;
            textarea.focus();
            textarea.dispatchEvent(new Event('input'));
          }
        }
      });
    }

    function checkGateState() {
      const isComplete = isLeadComplete(state.leadInfo);
      if (isComplete) {
        if (gateView) gateView.style.display = 'none';
        if (composerContainer) composerContainer.style.display = 'flex';
        
        const welcomeText = document.getElementById('sarathi-welcome-text');
        if (welcomeText && state.leadInfo.name) {
          const firstName = state.leadInfo.name.split(' ')[0];
          welcomeText.innerHTML = `<span class="sarathi-welcome-greeting">Hi <strong class="sarathi-user-firstname">${firstName}</strong> 👋</span><span class="sarathi-welcome-question">How can we empower you with intelligent technology today?</span>`;
        }

        renderChatHistory();
      } else {
        if (gateView) gateView.style.display = 'flex';
        if (welcomeScreen) welcomeScreen.style.display = 'none';
        if (messagesList) messagesList.style.display = 'none';
        if (composerContainer) composerContainer.style.display = 'none';
        if (gateName) setTimeout(() => gateName.focus(), 150);
      }
    }

    function handleGateSubmission() {
      const name = gateName ? gateName.value.trim() : '';
      const email = gateEmail ? gateEmail.value.trim() : '';
      const phone = gatePhone ? gatePhone.value.trim() : '';

      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const phoneClean = phone.replace(/[\s\-\(\)\+]/g, '');

      if (!name || !email || !phone) {
        if (gateError) {
          gateError.innerHTML = '<span>Please fill in Name, Email, and Phone Number.</span>';
          gateError.style.display = 'flex';
          gateError.classList.add('shake');
          setTimeout(() => gateError.classList.remove('shake'), 500);
        }
        return;
      }

      if (!emailRegex.test(email)) {
        if (gateError) {
          gateError.innerHTML = '<span>Please enter a valid email address.</span>';
          gateError.style.display = 'flex';
          gateError.classList.add('shake');
          setTimeout(() => gateError.classList.remove('shake'), 500);
        }
        if (gateEmail) gateEmail.focus();
        return;
      }

      if (phoneClean.length < 7) {
        if (gateError) {
          gateError.innerHTML = '<span>Please enter a valid phone number.</span>';
          gateError.style.display = 'flex';
          gateError.classList.add('shake');
          setTimeout(() => gateError.classList.remove('shake'), 500);
        }
        if (gatePhone) gatePhone.focus();
        return;
      }

      if (gateError) gateError.style.display = 'none';

      state.leadInfo = { 
        name, 
        email,
        phone,
        contact: `${email} | ${phone}`,
        interest: 'General Inquiry',
        timestamp: new Date().toISOString() 
      };
      safeStorage.setItem(CONFIG.storageKeyLead, JSON.stringify(state.leadInfo));

      // Post lead to webhook
      if (CONFIG.leadEndpoint) {
        const leadPayload = {
          name: name,
          email: email,
          phone: phone,
          contact: `${email} | ${phone}`,
          interest: 'General Inquiry',
          visitor_id: state.visitorId,
          conversation_id: state.conversationId,
          page_url: window.location.href,
          page_title: document.title,
          timestamp: new Date().toISOString()
        };

        const leadController = new AbortController();
        const leadTimeout = setTimeout(() => leadController.abort(), 12000);
        fetch(CONFIG.leadEndpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(leadPayload),
          signal: leadController.signal
        }).catch(err => {
          console.warn('[Sarathi AI] Lead capture webhook error:', err);
        }).finally(() => {
          clearTimeout(leadTimeout);
        });
      }

      checkGateState();
      playHapticTone('receive');
    }

    if (gateSubmit) {
      gateSubmit.addEventListener('click', handleGateSubmission);
    }

    [gateName, gateEmail, gatePhone].filter(Boolean).forEach(input => {
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          handleGateSubmission();
        }
      });
    });

    // Toggle Chat Window
    function toggleChat(force) {
      state.isOpen = typeof force === 'boolean' ? force : !state.isOpen;
      try {
        safeStorage.setItem(CONFIG.storageKeyOpen, state.isOpen ? 'true' : 'false');
      } catch (e) {}

      if (state.isOpen) {
        chatWindow.classList.add('open');
        if (launcherWrap) launcherWrap.classList.add('chat-open');
        if (launcherBtn) launcherBtn.classList.add('open');
        if (launcherPill) launcherPill.style.display = 'none';
        checkGateState();
        scrollToLatestExchange(false);
      } else {
        chatWindow.classList.remove('open');
        if (launcherWrap) launcherWrap.classList.remove('chat-open');
        if (launcherBtn) launcherBtn.classList.remove('open');
        if (launcherPill && !isLeadComplete(state.leadInfo)) {
          launcherPill.style.display = 'flex';
        }
      }
    }

    // Draggable Launcher Button & Header
    makeDraggable(
      launcherWrap,
      function(r, b) {
        chatWindow.style.right = r + 'px';
        chatWindow.style.bottom = b + 'px';
        safeStorage.setItem(CONFIG.storageKeyPos, JSON.stringify({ right: r, bottom: b }));
      },
      null,
      function() {
        toggleChat(true);
      }
    );

    makeDraggable(
      chatWindow,
      function(r, b) {
        launcherWrap.style.right = r + 'px';
        launcherWrap.style.bottom = b + 'px';
        safeStorage.setItem(CONFIG.storageKeyPos, JSON.stringify({ right: r, bottom: b }));
      },
      header
    );

    // Button Listeners
    if (launcherBtn) {
      launcherBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleChat();
      });
    }
    
    if (launcherPill) {
      launcherPill.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleChat(true);
      });
    }

    // Minimize Button (Button for minimizing the widget)
    if (btnMinimize) {
      const handleMinimize = (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleChat(false);
      };
      btnMinimize.addEventListener('click', handleMinimize);
      btnMinimize.addEventListener('pointerdown', (e) => e.stopPropagation());
    }

    if (btnClose) {
      const handleClose = (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleChat(false);
      };
      btnClose.addEventListener('click', handleClose);
      btnClose.addEventListener('pointerdown', (e) => e.stopPropagation());
    }

    // Topic Card Click Handlers
    document.querySelectorAll('.sarathi-topic-card').forEach(card => {
      card.addEventListener('click', (e) => {
        const topic = card.getAttribute('data-topic');
        if (topic) {
          handleSendMessage(topic);
        }
      });
    });

    // Popular Question Buttons Click Handlers
    document.querySelectorAll('.sarathi-popular-q-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const q = btn.getAttribute('data-question') || btn.querySelector('span').textContent.trim();
        if (q) {
          handleSendMessage(q);
        }
      });
    });

    // See All Popular Questions Handler
    const btnSeeAll = document.querySelector('.sarathi-popular-see-all');
    if (btnSeeAll) {
      btnSeeAll.addEventListener('click', () => {
        handleSendMessage('What services and solutions do you provide?');
      });
    }

    function submitUserMessage() {
      if (state.isTyping) {
        showToast('Please wait for Sarathi to finish responding');
        return;
      }
      const val = textarea.value.trim();
      if (val) {
        handleSendMessage(val);
        textarea.value = '';
        textarea.style.height = 'auto';
      } else {
        textarea.focus();
      }
    }

    // Textarea Auto-expand & Send Handling
    textarea.addEventListener('input', () => {
      textarea.style.height = 'auto';
      textarea.style.height = Math.min(textarea.scrollHeight, 90) + 'px';
    });

    textarea.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        submitUserMessage();
      }
    });

    btnSend.addEventListener('click', submitUserMessage);

    // Helper to check if chat conversation has started
    function hasChatStarted() {
      return Boolean(state.history && state.history.length > 0 && state.history.some(m => m.sender === 'user'));
    }

    // Dynamic AI Thinking Engine with Live Stopwatch & Multi-Stage Reasoning
    let thinkingInterval = null;
    let thinkingStartTime = 0;

    function startThinkingEngine() {
      const timerEl = document.getElementById('sarathi-thinking-timer');
      const stepTextEl = document.getElementById('sarathi-thinking-step-text');
      const stage1 = document.getElementById('sarathi-stage-1');
      const stage2 = document.getElementById('sarathi-stage-2');
      const stage3 = document.getElementById('sarathi-stage-3');
      const stage4 = document.getElementById('sarathi-stage-4');
      const conn1 = document.getElementById('sarathi-connector-1');
      const conn2 = document.getElementById('sarathi-connector-2');
      const conn3 = document.getElementById('sarathi-connector-3');

      if (thinkingInterval) {
        clearInterval(thinkingInterval);
        thinkingInterval = null;
      }

      thinkingStartTime = Date.now();

      const updateThinkingUI = () => {
        const elapsedMs = Date.now() - thinkingStartTime;
        const elapsedSec = (elapsedMs / 1000).toFixed(1);
        if (timerEl) timerEl.textContent = `${elapsedSec}s`;

        if (elapsedMs < 1200) {
          if (stepTextEl) stepTextEl.textContent = 'Analyzing question and intent...';
          if (stage1) stage1.className = 'sarathi-thinking-stage active';
          if (stage2) stage2.className = 'sarathi-thinking-stage';
          if (stage3) stage3.className = 'sarathi-thinking-stage';
          if (stage4) stage4.className = 'sarathi-thinking-stage';
          if (conn1) conn1.className = 'sarathi-thinking-connector';
          if (conn2) conn2.className = 'sarathi-thinking-connector';
          if (conn3) conn3.className = 'sarathi-thinking-connector';
        } else if (elapsedMs < 2800) {
          if (stepTextEl) stepTextEl.textContent = 'Accessing Sarathi AI knowledge base...';
          if (stage1) stage1.className = 'sarathi-thinking-stage completed';
          if (stage2) stage2.className = 'sarathi-thinking-stage active';
          if (stage3) stage3.className = 'sarathi-thinking-stage';
          if (stage4) stage4.className = 'sarathi-thinking-stage';
          if (conn1) conn1.className = 'sarathi-thinking-connector filled';
          if (conn2) conn2.className = 'sarathi-thinking-connector';
          if (conn3) conn3.className = 'sarathi-thinking-connector';
        } else if (elapsedMs < 4500) {
          if (stepTextEl) stepTextEl.textContent = 'Synthesizing neural reasoning & insights...';
          if (stage1) stage1.className = 'sarathi-thinking-stage completed';
          if (stage2) stage2.className = 'sarathi-thinking-stage completed';
          if (stage3) stage3.className = 'sarathi-thinking-stage active';
          if (stage4) stage4.className = 'sarathi-thinking-stage';
          if (conn1) conn1.className = 'sarathi-thinking-connector filled';
          if (conn2) conn2.className = 'sarathi-thinking-connector filled';
          if (conn3) conn3.className = 'sarathi-thinking-connector';
        } else {
          if (stepTextEl) stepTextEl.textContent = 'Formulating tailored, precise response...';
          if (stage1) stage1.className = 'sarathi-thinking-stage completed';
          if (stage2) stage2.className = 'sarathi-thinking-stage completed';
          if (stage3) stage3.className = 'sarathi-thinking-stage completed';
          if (stage4) stage4.className = 'sarathi-thinking-stage active';
          if (conn1) conn1.className = 'sarathi-thinking-connector filled';
          if (conn2) conn2.className = 'sarathi-thinking-connector filled';
          if (conn3) conn3.className = 'sarathi-thinking-connector filled';
        }
      };

      updateThinkingUI();
      thinkingInterval = setInterval(updateThinkingUI, 90);
    }

    function stopThinkingEngine() {
      if (thinkingInterval) {
        clearInterval(thinkingInterval);
        thinkingInterval = null;
      }
    }

    // Helper to send query to AI and stream response into history
    async function dispatchBotResponse(userText) {
      // Show advanced thinking indicator
      state.isTyping = true;
      if (typingWrap) typingWrap.style.display = 'block';
      startThinkingEngine();
      scrollToLatestExchange(true);

      // Send directly to n8n AI Agent Webhook
      try {
        let aiResult = null;

        if (CONFIG.apiEndpoint) {
          const payload = {
            message: userText.trim(),
            query: userText.trim(),
            chatInput: userText.trim(),
            prompt: userText.trim(),
            input: userText.trim(),
            visitor_id: state.visitorId,
            conversation_id: state.conversationId,
            sessionId: state.conversationId,
            name: (state.leadInfo && state.leadInfo.name) || '',
            email: (state.leadInfo && state.leadInfo.email) || '',
            phone: (state.leadInfo && (state.leadInfo.phone || state.leadInfo.contact)) || '',
            contact: (state.leadInfo && (state.leadInfo.phone || state.leadInfo.contact || state.leadInfo.email)) || '',
            page_url: window.location.href,
            page_title: document.title,
            timestamp: new Date().toISOString()
          };

          const controller = new AbortController();
          const timeoutId = setTimeout(() => controller.abort(), 25000);
          let response;

          try {
            response = await fetch(CONFIG.apiEndpoint, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload),
              signal: controller.signal
            });
          } finally {
            clearTimeout(timeoutId);
          }

          if (response.ok) {
            const contentType = response.headers.get('content-type') || '';
            let data;
            if (contentType.includes('application/json')) {
              data = await response.json();
            } else {
              const rawText = await response.text();
              try {
                data = JSON.parse(rawText);
              } catch {
                data = { answer: rawText };
              }
            }

            let textContent = '';
            if (typeof data === 'string') {
              textContent = data;
            } else if (data && typeof data === 'object') {
              const item = Array.isArray(data) ? data[0] : data;
              if (typeof item === 'string') {
                textContent = item;
              } else if (item && typeof item === 'object') {
                textContent = item.answer || item.output || item.response || item.text || item.message || item.content || (item.data && (item.data.answer || item.data.text || item.data.output)) || '';
              }
            }

            if (textContent && textContent.trim()) {
              const followUpChips = (data && data.chips && data.chips.length > 0)
                ? data.chips
                : getRecommendedQuestions(userText, textContent);

              aiResult = {
                category: 'Sarathi AI Agent',
                text: textContent.trim(),
                sources: (data && data.sources) || [],
                chips: followUpChips
              };
            }
          }
        }

        if (!aiResult) {
          await new Promise(r => setTimeout(r, 500));
          aiResult = generateFallbackResponse(userText);
        }

        // Ensure contextual recommended questions are provided for subsequent exchanges
        if (!aiResult.chips || aiResult.chips.length === 0) {
          aiResult.chips = getRecommendedQuestions(userText, aiResult.text || '');
        }

        const botMsg = {
          id: 'msg_' + (Date.now() + 1),
          sender: 'assistant',
          category: aiResult.category || 'AI Response',
          text: aiResult.text,
          sources: aiResult.sources || [],
          chips: aiResult.chips || [],
          time: formatTime(new Date()),
          feedback: null
        };

        state.history.push(botMsg);
        saveChatHistory();
        playHapticTone('receive');

      } catch (err) {
        console.warn('API error, falling back to local intelligence', err);
        const fallback = generateFallbackResponse(userText);
        state.history.push({
          id: 'msg_' + (Date.now() + 1),
          sender: 'assistant',
          category: fallback.category || 'AI Response',
          text: fallback.text,
          sources: fallback.sources,
          chips: getRecommendedQuestions(userText, fallback.text || ''),
          time: formatTime(new Date()),
          feedback: null
        });
        saveChatHistory();
        playHapticTone('receive');
      } finally {
        stopThinkingEngine();
        state.isTyping = false;
        if (typingWrap) typingWrap.style.display = 'none';
        renderChatHistory();
        scrollToLatestExchange(true);
      }
    }

    // Handle Sending Message
    async function handleSendMessage(userText) {
      if (!userText || state.isTyping) return;

      // If user is initiating chat for the first time, prepend the welcome greeting
      if (!hasChatStarted()) {
        const firstName = (state.leadInfo && state.leadInfo.name) ? state.leadInfo.name.split(' ')[0] : 'there';
        state.history.push({
          id: 'msg_welcome_' + Date.now(),
          sender: 'assistant',
          text: `Hi ${firstName} 👋\nHow can we empower you with intelligent technology today?`,
          time: formatTime(new Date())
        });
      }

      const userMsg = {
        id: 'msg_' + Date.now(),
        sender: 'user',
        text: userText,
        time: formatTime(new Date())
      };

      state.history.push(userMsg);
      saveChatHistory();
      renderChatHistory();
      scrollToLatestExchange(true);
      playHapticTone('send');

      await dispatchBotResponse(userText);
    }

    // Handle Editing Previous User Message
    async function handleEditMessage(msgId, newText) {
      if (!newText || state.isTyping) return;

      const msgIndex = state.history.findIndex(m => m.id === msgId);
      if (msgIndex === -1) return;

      // Update the user message and mark as edited
      state.history[msgIndex].text = newText;
      state.history[msgIndex].edited = true;
      state.history[msgIndex].time = formatTime(new Date());

      // Replace old response and subsequent messages after this point
      state.history = state.history.slice(0, msgIndex + 1);
      saveChatHistory();
      renderChatHistory();
      scrollToLatestExchange(true);
      playHapticTone('send');

      // Regenerate the response for the corrected message
      await dispatchBotResponse(newText);
    }

    // Render Conversation Stream (Matching Reference UI + Inline Editing)
    function renderChatHistory() {
      const isComplete = isLeadComplete(state.leadInfo);
      if (!isComplete) {
        if (gateView) gateView.style.display = 'flex';
        if (welcomeScreen) welcomeScreen.style.display = 'none';
        if (messagesList) messagesList.style.display = 'none';
        if (composerContainer) composerContainer.style.display = 'none';
        return;
      }

      if (gateView) gateView.style.display = 'none';
      if (composerContainer) composerContainer.style.display = 'flex';

      // Keep cards & popular questions visible until the user starts chatting
      if (!hasChatStarted()) {
        if (welcomeScreen) welcomeScreen.style.display = 'flex';
        if (messagesList) {
          messagesList.style.display = 'none';
          messagesList.innerHTML = '';
        }
        return;
      }

      // User has started chatting: switch from welcome cards to conversation stream
      if (welcomeScreen) welcomeScreen.style.display = 'none';
      if (messagesList) {
        messagesList.style.display = 'flex';
        messagesList.innerHTML = '';
      }

      state.history.forEach((msg) => {
        const isBot = msg.sender === 'assistant';
        const msgEl = document.createElement('div');
        msgEl.className = `sarathi-message ${isBot ? 'assistant' : 'user'}`;
        msgEl.id = msg.id;

        if (isBot) {
          msgEl.innerHTML = `
            <div class="sarathi-msg-bubble sarathi-bot-bubble">
              <div class="sarathi-markdown">${renderMarkdown(msg.text)}</div>
            </div>
          `;
        } else if (msg.id === currentEditingMsgId) {
          msgEl.innerHTML = `
            <div class="sarathi-user-bubble-wrapper is-editing">
              <div class="sarathi-inline-edit-box">
                <textarea class="sarathi-inline-edit-input" rows="1">${escapeHtml(msg.text)}</textarea>
                <div class="sarathi-inline-edit-actions">
                  <button type="button" class="sarathi-btn-cancel-edit">Cancel</button>
                  <button type="button" class="sarathi-btn-save-edit">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Update</span>
                  </button>
                </div>
              </div>
            </div>
          `;
        } else {
          msgEl.innerHTML = `
            <div class="sarathi-user-bubble-wrapper">
              <button type="button" class="sarathi-edit-msg-btn" data-msg-id="${msg.id}" title="Edit message" aria-label="Edit message">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
              </button>
              <div class="sarathi-msg-bubble sarathi-user-bubble">
                <span class="sarathi-user-text">${escapeHtml(msg.text)}</span>
                ${msg.edited ? '<span class="sarathi-edited-badge" title="Edited message">(edited)</span>' : ''}
              </div>
            </div>
          `;
        }

        messagesList.appendChild(msgEl);

        // Bind interactive events for user message editing
        if (!isBot) {
          if (msg.id === currentEditingMsgId) {
            const textareaEl = msgEl.querySelector('.sarathi-inline-edit-input');
            const cancelBtn = msgEl.querySelector('.sarathi-btn-cancel-edit');
            const saveBtn = msgEl.querySelector('.sarathi-btn-save-edit');

            if (textareaEl) {
              const autoResize = () => {
                textareaEl.style.height = 'auto';
                textareaEl.style.height = Math.min(textareaEl.scrollHeight, 160) + 'px';
              };
              setTimeout(() => {
                autoResize();
                textareaEl.focus();
                textareaEl.setSelectionRange(textareaEl.value.length, textareaEl.value.length);
              }, 40);

              textareaEl.addEventListener('input', autoResize);

              const cancel = () => {
                currentEditingMsgId = null;
                renderChatHistory();
              };

              const save = () => {
                const updatedText = textareaEl.value.trim();
                if (!updatedText) {
                  textareaEl.focus();
                  return;
                }
                currentEditingMsgId = null;
                if (updatedText === msg.text) {
                  renderChatHistory();
                  return;
                }
                handleEditMessage(msg.id, updatedText);
              };

              if (cancelBtn) cancelBtn.addEventListener('click', cancel);
              if (saveBtn) saveBtn.addEventListener('click', save);

              textareaEl.addEventListener('keydown', (ke) => {
                if (ke.key === 'Enter' && !ke.shiftKey) {
                  ke.preventDefault();
                  save();
                } else if (ke.key === 'Escape') {
                  ke.preventDefault();
                  cancel();
                }
              });
            }
          } else {
            const editBtn = msgEl.querySelector('.sarathi-edit-msg-btn');
            if (editBtn) {
              editBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (state.isTyping) {
                  showToast('Please wait for the current response to finish');
                  return;
                }
                currentEditingMsgId = msg.id;
                renderChatHistory();
              });
            }
          }
        }
      });

      scrollToLatestExchange(false);
    }

    function scrollToLatestExchange(smooth = false) {
      const body = document.getElementById('sarathi-body');
      if (!body) return;

      setTimeout(() => {
        body.scrollTo({
          top: body.scrollHeight,
          behavior: smooth ? 'smooth' : 'auto'
        });
      }, 40);
    }

    function formatTime(date) {
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(text) {
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    // Cross-Tab Synchronization
    window.addEventListener('storage', (e) => {
      if (e.key === CONFIG.storageKeyHistory) {
        if (state.isTyping || currentEditingMsgId) return;
        state.history = getChatHistory();
        renderChatHistory();
        scrollToLatestExchange(false);
      }
      if (e.key === CONFIG.storageKeyLead) {
        state.leadInfo = getLeadInfo();
        checkGateState();
      }
    });

    window.addEventListener('focus', () => {
      if (state.isTyping || currentEditingMsgId) return;
      const latestHistory = getChatHistory();
      if (JSON.stringify(latestHistory) !== JSON.stringify(state.history)) {
        state.history = latestHistory;
        renderChatHistory();
      }
    });

    // Helper for clipboard copy with fallback
    function copyTextToClipboard(text, onSuccess) {
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(onSuccess).catch(() => {
          fallbackCopyText(text, onSuccess);
        });
      } else {
        fallbackCopyText(text, onSuccess);
      }
    }

    function fallbackCopyText(text, onSuccess) {
      try {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.top = '-9999px';
        ta.style.left = '-9999px';
        ta.setAttribute('readonly', '');
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        const successful = document.execCommand('copy');
        document.body.removeChild(ta);
        if (successful && onSuccess) onSuccess();
      } catch (e) {}
    }

    // Ensure clicking any internal link in the chat window marks the widget to remain open across page navigation
    if (chatWindow) {
      chatWindow.addEventListener('click', (e) => {
        // Code Block Copy Button Handler
        const copyBtn = e.target.closest('.sarathi-code-copy-btn');
        if (copyBtn) {
          e.preventDefault();
          e.stopPropagation();
          const codeId = copyBtn.getAttribute('data-code-id');
          const codeEl = codeId ? document.getElementById(codeId) : copyBtn.closest('.sarathi-code-container')?.querySelector('code');
          if (codeEl) {
            const textToCopy = codeEl.textContent || '';
            copyTextToClipboard(textToCopy, () => {
              const label = copyBtn.querySelector('span');
              if (label) label.textContent = 'Copied! ✓';
              copyBtn.classList.add('copied');
              showToast('Code copied to clipboard! 📋');
              setTimeout(() => {
                if (label) label.textContent = 'Copy';
                copyBtn.classList.remove('copied');
              }, 2000);
            });
          }
          return;
        }

        const link = e.target.closest('a[href]');
        if (link) {
          const target = link.getAttribute('target');
          if (!target || target === '_self') {
            try {
              safeStorage.setItem(CONFIG.storageKeyOpen, 'true');
            } catch (err) {}
          }
        }
      });
    }

    // Render Initial State
    renderChatHistory();

    // Auto-restore chat open state across pages if previously open
    if (state.isOpen) {
      toggleChat(true);
    }
  }

  // Draggable Utility
  function makeDraggable(element, onMoveEnd, handleElement, onClick) {
    let isDragging = false;
    let startX = 0, startY = 0;
    let initialRight = 0, initialBottom = 0;
    let hasMoved = false;

    const dragHandle = handleElement || element;

    dragHandle.addEventListener('pointerdown', onStart);

    function onStart(e) {
      if (window.innerWidth <= 768) return; // Disable dragging on mobile devices
      if (element.classList.contains('sarathi-fullscreen-mode')) return; // Disable dragging in fullscreen
      if (e.target.closest('.sarathi-icon-btn') || e.target.closest('.sarathi-chat-header-actions')) {
        return;
      }

      isDragging = true;
      hasMoved = false;
      startX = e.clientX;
      startY = e.clientY;

      const rect = element.getBoundingClientRect();
      initialRight = window.innerWidth - rect.right;
      initialBottom = window.innerHeight - rect.bottom;

      try {
        dragHandle.setPointerCapture(e.pointerId);
      } catch (err) {}

      dragHandle.addEventListener('pointermove', onMove);
      dragHandle.addEventListener('pointerup', onEnd);
      dragHandle.addEventListener('pointercancel', onEnd);
    }

    function onMove(e) {
      if (!isDragging) return;
      const dx = e.clientX - startX;
      const dy = e.clientY - startY;

      if (!hasMoved && (Math.abs(dx) > 4 || Math.abs(dy) > 4)) {
        hasMoved = true;
        element.classList.add('is-dragging');
      }

      if (!hasMoved) return;
      if (e.cancelable) e.preventDefault();

      let newRight = initialRight - dx;
      let newBottom = initialBottom - dy;

      const margin = 12;
      const maxRight = Math.max(margin, window.innerWidth - element.offsetWidth - margin);
      const maxBottom = Math.max(margin, window.innerHeight - element.offsetHeight - margin);

      newRight = Math.max(margin, Math.min(maxRight, newRight));
      newBottom = Math.max(margin, Math.min(maxBottom, newBottom));

      element.style.right = newRight + 'px';
      element.style.bottom = newBottom + 'px';
      element.style.left = 'auto';
      element.style.top = 'auto';

      if (onMoveEnd) {
        onMoveEnd(newRight, newBottom);
      }
    }

    function onEnd(e) {
      if (!isDragging) return;
      isDragging = false;
      element.classList.remove('is-dragging');

      try {
        dragHandle.releasePointerCapture(e.pointerId);
      } catch (err) {}

      dragHandle.removeEventListener('pointermove', onMove);
      dragHandle.removeEventListener('pointerup', onEnd);
      dragHandle.removeEventListener('pointercancel', onEnd);

      if (hasMoved) {
        const stopClick = (clickEvent) => {
          clickEvent.stopPropagation();
          clickEvent.preventDefault();
          window.removeEventListener('click', stopClick, true);
        };
        window.addEventListener('click', stopClick, true);

        const rect = element.getBoundingClientRect();
        const r = window.innerWidth - rect.right;
        const b = window.innerHeight - rect.bottom;
        if (onMoveEnd) onMoveEnd(r, b);
      } else {
        if (onClick) onClick(e);
      }
    }
  }

  // Initialize on DOM Ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initChatbot);
  } else {
    initChatbot();
  }
})();
