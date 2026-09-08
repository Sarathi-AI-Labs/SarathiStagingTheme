/**
 * Sarathi AI Labs - Ultra-Premium Next-Gen Intelligent Concierge Chatbot
 * Brand: Sarathi AI Labs | Intelligent IT Solutions & Training
 */

(function () {
  'use strict';

  // Configuration
  const CONFIG = {
    apiEndpoint: (window.SARATHI_CHATBOT_SETTINGS && window.SARATHI_CHATBOT_SETTINGS.chatWebhookUrl) || window.SARATHI_CHATBOT_API || 'https://n8n.srv1178467.hstgr.cloud/webhook/sal-ai-chat',
    leadEndpoint: (window.SARATHI_CHATBOT_SETTINGS && window.SARATHI_CHATBOT_SETTINGS.leadWebhookUrl) || window.SARATHI_LEAD_API || 'https://n8n.srv1178467.hstgr.cloud/webhook/sal-lead-cap',
    fullscreenUrl: window.SARATHI_FULLSCREEN_URL || '/chatbot-fullscreen.html',
    brandName: 'Sarathi AI Labs',
    brandTagline: 'Intelligent IT Solutions & Training',
    botName: 'Sarathi AI',
    botIconUrl: (window.SARATHI_THEME_URI || '/wp-content/themes/custom-theme') + '/assets/images/sarathi-bot-transparent.png?v=3',
    storageKeyHistory: 'sarathi_ai_chat_history',
    storageKeyLead: 'sarathi_ai_lead_info',
    storageKeyPos: 'sarathi_ai_widget_pos',
    storageKeySession: 'sarathi_ai_conv_id',
    storageKeyTheme: 'sarathi_ai_theme',
    storageKeyAudio: 'sarathi_ai_audio_enabled'
  };

  // State
  const state = {
    isOpen: false,
    leadCaptured: false,
    theme: localStorage.getItem(CONFIG.storageKeyTheme) || 'light',
    audioEnabled: localStorage.getItem(CONFIG.storageKeyAudio) !== 'false',
    selectedInterest: '',
    visitorId: getOrCreateVisitorId(),
    conversationId: getOrCreateConversationId(),
    leadInfo: getLeadInfo(),
    history: getChatHistory(),
    isTyping: false,
    speakingId: null
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

  // Text-To-Speech Speech Synthesis Engine
  function speakMessage(text, msgId, onEnd) {
    if (!('speechSynthesis' in window)) return;
    window.speechSynthesis.cancel();

    if (state.speakingId === msgId) {
      state.speakingId = null;
      if (onEnd) onEnd();
      return;
    }

    // Strip markdown formatting for speech
    const cleanText = text
      .replace(/###?\s*/g, '')
      .replace(/\*\*/g, '')
      .replace(/```[\s\S]*?```/g, 'Code block omitted.')
      .replace(/`([^`]+)`/g, '$1')
      .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
      .replace(/\|.*?\|/g, '')
      .replace(/[-*]\s+/g, '');

    const utterance = new SpeechSynthesisUtterance(cleanText);
    utterance.rate = 1.05;
    utterance.pitch = 1.0;
    
    state.speakingId = msgId;

    utterance.onend = () => {
      state.speakingId = null;
      if (onEnd) onEnd();
    };
    utterance.onerror = () => {
      state.speakingId = null;
      if (onEnd) onEnd();
    };

    window.speechSynthesis.speak(utterance);
  }

  // Helper ID generators
  function getOrCreateVisitorId() {
    let vid = localStorage.getItem('sarathi_ai_visitor_id');
    if (!vid) {
      vid = 'vid_' + Math.random().toString(36).substring(2, 10) + Date.now().toString(36);
      localStorage.setItem('sarathi_ai_visitor_id', vid);
    }
    return vid;
  }

  function getOrCreateConversationId() {
    let cid = localStorage.getItem(CONFIG.storageKeySession);
    if (!cid) {
      cid = 'conv_' + Math.random().toString(36).substring(2, 10) + Date.now().toString(36);
      localStorage.setItem(CONFIG.storageKeySession, cid);
    }
    return cid;
  }

  function getLeadInfo() {
    try {
      return JSON.parse(localStorage.getItem(CONFIG.storageKeyLead) || '{}');
    } catch (e) {
      return {};
    }
  }

  function getChatHistory() {
    try {
      return JSON.parse(localStorage.getItem(CONFIG.storageKeyHistory) || '[]');
    } catch (e) {
      return [];
    }
  }

  function saveChatHistory() {
    try {
      localStorage.setItem(CONFIG.storageKeyHistory, JSON.stringify(state.history));
    } catch (e) {
      console.error('Failed to save chat history', e);
    }
  }

  function getTimeGreeting() {
    const hr = new Date().getHours();
    if (hr < 12) return 'Good morning';
    if (hr < 17) return 'Good afternoon';
    return 'Good evening';
  }

  // Safe DOM-based High-Performance Markdown & Feature Parser
  function renderMarkdown(text) {
    if (!text) return '';
    
    // Normalize newlines
    let raw = text.replace(/\r\n/g, '\n');

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

    // Callouts / Alerts (> [!NOTE], > [!TIP], > [!IMPORTANT], > [!WARNING])
    html = html.replace(/^&gt;\s*\[!(NOTE|TIP|IMPORTANT|WARNING|CAUTION)\]\s*(.*)$/gim, function (match, type, content) {
      const typeLower = type.toLowerCase();
      const icons = {
        note: 'ℹ️',
        tip: '💡',
        important: '⭐',
        warning: '⚠️',
        caution: '🚨'
      };
      return `<div class="sarathi-callout sarathi-callout-${typeLower}"><div class="sarathi-callout-icon">${icons[typeLower] || '💡'}</div><div class="sarathi-callout-body"><span class="sarathi-callout-title">${type}</span><p class="sarathi-callout-text">${content.trim()}</p></div></div>`;
    });

    // Links [title](url)
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="sarathi-md-link" target="_blank" rel="noopener noreferrer">$1 <span class="sarathi-link-arrow">↗</span></a>');

    // Standard Blockquote
    html = html.replace(/^&gt;\s*(.*$)/gim, '<blockquote class="sarathi-blockquote"><span class="quote-glyph">“</span><p>$1</p></blockquote>');

    // Line-by-line Smart List, Step Numbers & Feature Card Formatter
    const lines = html.split('\n');
    let out = [];
    let featureBuffer = [];
    let stepBuffer = [];
    let listBuffer = [];

    function flushFeatures() {
      if (featureBuffer.length === 0) return;
      let fHtml = '<div class="sarathi-feature-list">';
      featureBuffer.forEach((f, fIdx) => {
        const delay = (fIdx * 0.04).toFixed(2);
        fHtml += `
          <div class="sarathi-feature-card" style="animation-delay: ${delay}s;">
            <div class="sarathi-feature-icon-box">${f.icon}</div>
            <div class="sarathi-feature-body">
              <span class="sarathi-feature-title">${f.title}</span>
              ${f.desc ? `<span class="sarathi-feature-desc">${f.desc}</span>` : ''}
            </div>
          </div>
        `;
      });
      fHtml += '</div>';
      out.push(fHtml);
      featureBuffer = [];
    }

    function flushSteps() {
      if (stepBuffer.length === 0) return;
      let sHtml = '<div class="sarathi-step-list">';
      stepBuffer.forEach((s, sIdx) => {
        const delay = (sIdx * 0.05).toFixed(2);
        const formattedNum = String(s.num).padStart(2, '0');
        sHtml += `
          <div class="sarathi-step-card" style="animation-delay: ${delay}s;">
            <div class="sarathi-step-num-badge">${formattedNum}</div>
            <div class="sarathi-step-body">
              <span class="sarathi-step-title">${s.title}</span>
              ${s.desc ? `<span class="sarathi-step-desc">${s.desc}</span>` : ''}
            </div>
          </div>
        `;
      });
      sHtml += '</div>';
      out.push(sHtml);
      stepBuffer = [];
    }

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

    function flushAll() {
      flushFeatures();
      flushSteps();
      flushList();
    }

    // Emoji pattern regex supporting modern unicode emojis
    const emojiRegex = /^([\u{1F300}-\u{1F9FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}\u{1F1E0}-\u{1F1FF}\u{1F600}-\u{1F64F}\u{1F680}-\u{1F6FF}\u{1F900}-\u{1F9FF}\u{1FA70}-\u{1FAFF}])\s*/u;

    lines.forEach((line) => {
      const trimmed = line.trim();
      if (!trimmed) {
        flushAll();
        return;
      }

      // If it's already an HTML block tag (table, code, header, blockquote, callout)
      if (trimmed.startsWith('<div class="sarathi-code') || trimmed.startsWith('<div class="sarathi-table') || trimmed.startsWith('<div class="sarathi-callout') || trimmed.startsWith('<h') || trimmed.startsWith('<blockquote')) {
        flushAll();
        out.push(trimmed);
        return;
      }

      // Check for Numbered Steps: "1. **Title**: Description" or "1. Step description"
      const numberStepMatch = trimmed.match(/^(\d+)\.\s+(.*)$/);
      if (numberStepMatch) {
        flushFeatures();
        flushList();
        const stepNum = numberStepMatch[1];
        const stepContent = numberStepMatch[2];
        const strongMatch = stepContent.match(/^(?:<strong>([^<]+)<\/strong>|\*\*([^*]+)\*\*)\s*(?:[–—:-]\s*|\s*:\s*|\s*-\s*)(.*)$/);
        
        if (strongMatch) {
          stepBuffer.push({
            num: stepNum,
            title: strongMatch[1] || strongMatch[2],
            desc: strongMatch[3]
          });
        } else {
          stepBuffer.push({
            num: stepNum,
            title: stepContent,
            desc: ''
          });
        }
        return;
      }

      // Check for Emoji or bullet feature lines
      const emojiMatch = trimmed.match(emojiRegex);
      const bulletMatch = trimmed.match(/^[-*•]\s+/);

      let isFeatureItem = false;
      let itemIcon = '✦';
      let itemTitle = '';
      let itemDesc = '';

      if (emojiMatch) {
        itemIcon = emojiMatch[1];
        const rest = trimmed.substring(emojiMatch[0].length).trim();
        const strongMatch = rest.match(/^(?:<strong>([^<]+)<\/strong>|\*\*([^*]+)\*\*)\s*(?:[–—:-]\s*|\s*:\s*|\s*-\s*)(.*)$/);
        const plainMatch = rest.match(/^([A-Za-z0-9\s&/]{2,40})\s*(?:[–—:-]\s*|\s*:\s*|\s*-\s*)(.*)$/);
        
        if (strongMatch) {
          itemTitle = strongMatch[1] || strongMatch[2];
          itemDesc = strongMatch[3];
          isFeatureItem = true;
        } else if (plainMatch) {
          itemTitle = plainMatch[1];
          itemDesc = plainMatch[2];
          isFeatureItem = true;
        } else {
          itemTitle = rest;
          itemDesc = '';
          isFeatureItem = true;
        }
      } else if (bulletMatch) {
        const rest = trimmed.substring(bulletMatch[0].length).trim();
        const bulletEmoji = rest.match(emojiRegex);
        if (bulletEmoji) {
          itemIcon = bulletEmoji[1];
          const postEmoji = rest.substring(bulletEmoji[0].length).trim();
          const strongMatch = postEmoji.match(/^(?:<strong>([^<]+)<\/strong>|\*\*([^*]+)\*\*)\s*(?:[–—:-]\s*|\s*:\s*|\s*-\s*)(.*)$/);
          if (strongMatch) {
            itemTitle = strongMatch[1] || strongMatch[2];
            itemDesc = strongMatch[3];
            isFeatureItem = true;
          }
        } else {
          const strongMatch = rest.match(/^(?:<strong>([^<]+)<\/strong>|\*\*([^*]+)\*\*)\s*(?:[–—:-]\s*|\s*:\s*|\s*-\s*)(.*)$/);
          if (strongMatch) {
            itemIcon = '🔹';
            itemTitle = strongMatch[1] || strongMatch[2];
            itemDesc = strongMatch[3];
            isFeatureItem = true;
          }
        }
      }

      if (isFeatureItem) {
        flushSteps();
        flushList();
        featureBuffer.push({ icon: itemIcon, title: itemTitle, desc: itemDesc });
        return;
      }

      // Check for regular bullet list
      if (bulletMatch) {
        flushFeatures();
        flushSteps();
        listBuffer.push(trimmed.substring(bulletMatch[0].length).trim());
        return;
      }

      flushAll();

      // Check if it's a closing prompt or question (e.g. "What area would you like to explore first?")
      const isClosingQuestion = (trimmed.endsWith('?') && (trimmed.startsWith('What') || trimmed.startsWith('How') || trimmed.startsWith('Would you') || trimmed.startsWith('Which') || trimmed.startsWith('Feel free') || trimmed.startsWith('Can I') || trimmed.startsWith('Shall we')));
      if (isClosingQuestion) {
        out.push(`<div class="sarathi-md-prompt"><span class="prompt-spark">💡</span><span>${trimmed}</span></div>`);
      } else {
        out.push(`<p class="sarathi-md-p">${trimmed}</p>`);
      }
    });

    flushAll();

    return out.join('');
  }

  // Intelligent Sarathi AI Knowledge Engine
  function generateFallbackResponse(query) {
    const q = query.toLowerCase().trim();

    if (q.includes('agentic') || q.includes('autonomous') || q.includes('llm') || q.includes('custom solution') || q.includes('rag') || q.includes('swarm')) {
      return {
        category: 'Autonomous Agentic Systems',
        text: `### 🤖 Sarathi Agentic AI & Custom Autonomous Systems

At **Sarathi AI Labs**, we engineer multi-agent swarms and production-grade RAG architectures that execute complex enterprise workflows autonomously:

- **Multi-Agent Orchestration**: Goal-directed autonomous workflows engineered with LangGraph, CrewAI, AutoGen, and Temporal.
- **Hybrid Enterprise RAG**: Dual dense + sparse vector search with re-ranking (Qdrant/Pinecone) for zero-hallucination domain knowledge retrieval.
- **Autonomous Tool Calling**: Deterministic API integrations, self-healing database queries, and CRM/ERP orchestrations.

| Capability Matrix | Enterprise Impact | Core Tech Stack |
| :--- | :--- | :--- |
| **Multi-Agent Swarms** | 10x Operational Speed | LangGraph, Python, Temporal |
| **Hybrid Neural RAG** | 99.7% Retrieval Precision | Qdrant, Pinecone, Cohere |
| **Self-Healing Automation** | Zero Human Intervention | Playwright, n8n, FastAPI |

\`\`\`python
# Sarathi Agentic Multi-Agent Workflow Blueprint
from langchain_core.agents import AgentExecutor
from sarathi_ai.core import MultiAgentOrchestrator

orchestrator = MultiAgentOrchestrator(
    agents=["researcher", "qa_architect", "solution_builder"],
    rag_backend="qdrant_hybrid",
    telemetry=True
)
execution_result = orchestrator.solve_goal("Automate regression audit pipeline")
\`\`\`

Would you like to schedule an **Agentic AI Architecture Consultation** with our principal architects?`,
        sources: [
          { title: 'Sarathi Agentic AI Blueprint', url: '#agentic-ai' },
          { title: 'Schedule Architecture Session', url: '#contact' }
        ],
        chips: ['Book Architecture Call', 'Full-Stack Web Systems', 'Test Automation QA', 'Talk to Human']
      };
    }

    if (q.includes('web') || q.includes('fullstack') || q.includes('full stack') || q.includes('frontend') || q.includes('backend') || q.includes('saas') || q.includes('cloud')) {
      return {
        category: 'Full-Stack & Cloud Architecture',
        text: `### 💻 Full-Stack Web Development & Cloud Systems

We engineer enterprise-grade web applications with uncompromising performance, reliability, and security:

1. **Modern Frontend & Jamstack**: Next.js 15 (App Router), React Server Components, High-Performance UI, WordPress Headless.
2. **Cloud Microservices & APIs**: High-throughput distributed backends in Python (FastAPI), Node.js, Go, and PHP.
3. **Cloud Infrastructure & Scale**: Automated CI/CD pipelines, Docker/Kubernetes containerization, AWS/GCP, and PostgreSQL/Redis caching.

> All digital products built by Sarathi AI Labs achieve 95+ Google Lighthouse scores, robust OWASP Top 10 security compliance, and automated quality gates.`,
        sources: [
          { title: 'Enterprise Web Engineering Portfolio', url: '#web-dev' },
          { title: 'Request Technical Proposal', url: '#contact' }
        ],
        chips: ['Request a Proposal', 'Agentic AI Solutions', 'QA Test Automation', 'Contact Tech Team']
      };
    }

    if (q.includes('test') || q.includes('automation') || q.includes('qa') || q.includes('selenium') || q.includes('playwright') || q.includes('cypress')) {
      return {
        category: 'Enterprise Quality Engineering',
        text: `### ⚡ Enterprise Test Automation & Continuous Quality Gates

Eliminate regression escapes and accelerate your delivery cadence with our end-to-end automated QA frameworks:

- **End-to-End Test Automation**: Robust cross-browser test suites using Playwright, Cypress, and Selenium WebDriver.
- **High-Throughput API & Load Testing**: k6 distributed load simulation, Postman/Newman automated contract verification.
- **CI/CD Quality Gates**: Automated parallel test pipelines in GitHub Actions, GitLab CI, and Jenkins.
- **AI-Powered Visual Regression**: Automated visual diff detection across all screen resolutions and devices.

*Typical Outcome: 70%+ reduction in regression turnaround time with 99.8% test repeatability.*`,
        sources: [
          { title: 'QA Automation Framework Architecture', url: '#test-automation' },
          { title: 'Schedule Quality Process Audit', url: '#contact' }
        ],
        chips: ['Request QA Audit', 'Training Bootcamps', 'Agentic AI Solutions', 'Contact Us']
      };
    }

    if (q.includes('training') || q.includes('course') || q.includes('bootcamp') || q.includes('learn') || q.includes('curriculum') || q.includes('cohort')) {
      return {
        category: 'Professional Engineering Training',
        text: `### 🎓 Industry-Ready Bootcamps & Corporate Engineering Training

Accelerate your engineering teams or master high-demand modern technologies with our intensive practitioner-led programs:

| Program Cohort | Duration | Core Engineering Focus |
| :--- | :--- | :--- |
| **Agentic AI & LLM Engineering** | 8 Weeks | LangChain, LangGraph, Hybrid RAG, Autonomous Agents |
| **Full-Stack Web Mastery** | 12 Weeks | Next.js, Cloud APIs, Microservices, Distributed Systems |
| **Enterprise Test Automation** | 6 Weeks | Playwright, CI/CD Architecture, Framework Design |

- **100% Real-World Enterprise Projects**: Build, test, and deploy production architectures.
- **1-on-1 Mentor Guidance**: Direct reviews from Principal Engineers at Sarathi AI Labs.`,
        sources: [
          { title: 'View Course Catalog & Cohort Dates', url: '#training' },
          { title: 'Enroll for Upcoming Cohort', url: '#contact' }
        ],
        chips: ['Enroll in Next Cohort', 'Corporate Training Inquiry', 'Agentic AI Blueprint', 'Contact Tech Team']
      };
    }

    if (q.includes('contact') || q.includes('talk') || q.includes('human') || q.includes('phone') || q.includes('email') || q.includes('hire') || q.includes('quote')) {
      return {
        category: 'Connect with Solutions Team',
        text: `### 💬 Connect with the Sarathi AI Labs Team

We look forward to collaborating with you on your next mission-critical initiative:

- **Email**: \`contact@sarathiai.com\`
- **Response SLA**: Within 2 business hours
- **Office Hours**: Monday – Friday, 9:00 AM – 6:00 PM IST
- **Complimentary Session**: 30-minute system architecture and AI feasibility review.

Please drop your contact details in the composer or use our direct scheduling link below!`,
        sources: [
          { title: 'Direct Architecture Booking Link', url: '#contact' }
        ],
        chips: ['Book Free Consultation', 'Agentic AI Solutions', 'Training Bootcamps']
      };
    }

    // Default Intelligence Response
    return {
      category: 'Sarathi Neural Concierge',
      text: `Hello! I am the **Sarathi Neural Concierge**. How can we help accelerate your technology roadmap today?

- **🤖 Agentic AI & Custom Solutions** (Multi-agent workflows, Hybrid RAG, Autonomous LLMs)
- **💻 Full-Stack & Cloud Architecture** (Next.js, High-performance Web Systems, APIs)
- **⚡ Enterprise Test Automation** (Playwright, CI/CD Quality Gates, Load Testing)
- **🎓 Industry-Ready Professional Courses** (Hands-on corporate & developer programs)

Select a topic above or ask any technical question!`,
      sources: [
        { title: 'Explore Sarathi AI Labs Platform', url: '/' }
      ],
      chips: ['Agentic AI Solutions', 'Web Development', 'Test Automation', 'Professional Courses', 'Contact Tech Team']
    };
  }

  // Build Floating UI DOM with Scoped High-Tech Design
  function createWidgetDOM() {
    const root = document.createElement('div');
    root.id = 'sarathi-ai-root';
    root.setAttribute('data-theme', state.theme);

    root.innerHTML = `
      <!-- Launcher Mascot with Hello! Bubble -->
      <div class="sarathi-launcher-container" id="sarathi-launcher-wrap">
        <button class="sarathi-launcher-btn" id="sarathi-launcher-btn" aria-label="Open Sarathi AI Chatbot">
          <img src="${CONFIG.botIconUrl}" alt="Sarathi AI" class="sarathi-launcher-mascot-img" />
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
        
        <!-- Header (Clean & Minimalist) -->
        <div class="sarathi-chat-header" id="sarathi-header">
          <div class="sarathi-chat-header-info">
            <div class="sarathi-avatar-wrap">
              <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="17" r="5" fill="#38BDF8"/>
                <rect x="47.5" y="21" width="5" height="7" rx="2.5" fill="#FFFFFF"/>
                <rect x="18" y="44" width="7" height="18" rx="3.5" fill="#FFFFFF"/>
                <rect x="75" y="44" width="7" height="18" rx="3.5" fill="#FFFFFF"/>
                <rect x="23" y="27" width="54" height="49" rx="19" fill="#FFFFFF"/>
                <rect x="29" y="33" width="42" height="37" rx="13" fill="#0F172A"/>
                <circle cx="41.5" cy="49" r="4.5" fill="#38BDF8"/>
                <circle cx="58.5" cy="49" r="4.5" fill="#38BDF8"/>
                <path d="M 44.5 56.5 Q 50 62 55.5 56.5" stroke="#38BDF8" stroke-width="2.5" stroke-linecap="round" fill="none"/>
              </svg>
              <div class="online-indicator"></div>
            </div>
            <div class="sarathi-chat-header-text">
              <h3>Sarathi AI</h3>
              <p class="sarathi-chat-header-subtitle">
                <span class="sarathi-live-dot"></span> Online
              </p>
            </div>
          </div>
          
          <div class="sarathi-chat-header-actions">
            <button class="sarathi-icon-btn" id="sarathi-btn-theme" title="Toggle Theme" aria-label="Toggle Theme">
              ${state.theme === 'dark' ? 
                `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>` : 
                `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>`}
            </button>
            <button class="sarathi-icon-btn" id="sarathi-btn-reset" title="Reset Chat" aria-label="Reset Conversation">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                <path d="M3 3v5h5"/>
              </svg>
            </button>
            <button class="sarathi-icon-btn" id="sarathi-btn-fullscreen" title="Fullscreen" aria-label="Fullscreen">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                <polyline points="15 3 21 3 21 9"/>
                <line x1="10" y1="14" x2="21" y2="3"/>
              </svg>
            </button>
            <button class="sarathi-icon-btn sarathi-btn-close" id="sarathi-btn-close" title="Close" aria-label="Close Chat">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Body / Screens Container -->
        <div class="sarathi-chat-body" id="sarathi-body">
          
          <!-- Screen 1: Mandatory Gatekeeper Screen -->
          <div class="sarathi-gate-view" id="sarathi-gate-view" style="${state.leadInfo && state.leadInfo.name && state.leadInfo.contact ? 'display: none;' : 'display: flex;'}">
            
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
                <input type="text" id="sarathi-gate-contact" class="sarathi-gate-input" placeholder="Work Email or Phone" required autocomplete="email">
              </div>

              <div class="sarathi-gate-error" id="sarathi-gate-error">
                <span>Please fill in both fields to continue.</span>
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

          <!-- Screen 2: Clean Minimalist Welcome Hub -->
          <div class="sarathi-welcome-screen" id="sarathi-welcome-screen" style="${state.leadInfo && state.leadInfo.name && state.leadInfo.contact && state.history.length === 0 ? 'display: flex;' : 'display: none;'}">

            <div class="sarathi-hub-header">
              <h2 id="sarathi-hub-title">Hi ${state.leadInfo && state.leadInfo.name ? `<span class="sarathi-gradient-name">${state.leadInfo.name.split(' ')[0]}</span>` : 'there'} 👋</h2>
              <p>How can I empower you or your business today?</p>
            </div>

            <!-- Clean Topic Cards Grid -->
            <div class="sarathi-topic-grid">
              
              <div class="sarathi-topic-card" data-topic="Agentic AI & Custom Solutions" style="--card-delay: 0.04s;">
                <div class="sarathi-topic-icon-badge badge-blue">🤖</div>
                <div class="sarathi-topic-info">
                  <h4>Agentic AI Solutions</h4>
                  <p>Autonomous AI agents & custom RAG systems</p>
                </div>
                <span class="sarathi-topic-arrow">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </span>
              </div>

              <div class="sarathi-topic-card" data-topic="Web Development" style="--card-delay: 0.08s;">
                <div class="sarathi-topic-icon-badge badge-green">💻</div>
                <div class="sarathi-topic-info">
                  <h4>Web Development & Cloud</h4>
                  <p>Modern full-stack web apps & cloud APIs</p>
                </div>
                <span class="sarathi-topic-arrow">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </span>
              </div>

              <div class="sarathi-topic-card" data-topic="Test Automation" style="--card-delay: 0.12s;">
                <div class="sarathi-topic-icon-badge badge-amber">⚡</div>
                <div class="sarathi-topic-info">
                  <h4>Test Automation & QA</h4>
                  <p>Playwright, CI/CD quality engineering</p>
                </div>
                <span class="sarathi-topic-arrow">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </span>
              </div>

              <div class="sarathi-topic-card" data-topic="Professional Courses" style="--card-delay: 0.16s;">
                <div class="sarathi-topic-icon-badge badge-purple">🎓</div>
                <div class="sarathi-topic-info">
                  <h4>Professional Courses</h4>
                  <p>Industry-ready engineering & tech courses</p>
                </div>
                <span class="sarathi-topic-arrow">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </span>
              </div>

            </div>

          </div>

          <!-- Screen 3: Messages Stream Container -->
          <div class="sarathi-chat-messages" id="sarathi-messages-list" style="${state.leadInfo && state.leadInfo.name && state.leadInfo.contact && state.history.length > 0 ? 'display: flex;' : 'display: none;'}">
            <!-- Messages injected dynamically -->
          </div>

          <!-- Dynamic Streaming / Typing Indicator -->
          <div id="sarathi-typing-wrap" class="sarathi-typing-wrapper" style="display: none;">
            <div class="sarathi-typing-bubble">
              <div class="sarathi-typing-avatar-mini">
                <svg viewBox="0 0 100 100" fill="none">
                  <circle cx="50" cy="17" r="5" fill="#38BDF8"/>
                  <rect x="47.5" y="21" width="5" height="7" rx="2.5" fill="#FFFFFF"/>
                  <rect x="23" y="27" width="54" height="49" rx="19" fill="#FFFFFF"/>
                  <rect x="29" y="33" width="42" height="37" rx="13" fill="#0F172A"/>
                  <circle cx="41.5" cy="49" r="4.5" fill="#38BDF8"/>
                  <circle cx="58.5" cy="49" r="4.5" fill="#38BDF8"/>
                </svg>
              </div>
              <div class="sarathi-typing-dots">
                <span></span>
                <span></span>
                <span></span>
              </div>
              <span class="sarathi-typing-text">Synthesizing intelligent response...</span>
            </div>
          </div>

        </div>

        <!-- Composer / Input Container -->
        <div class="sarathi-composer-container" id="sarathi-composer-container" style="${state.leadInfo && state.leadInfo.name && state.leadInfo.contact ? 'display: flex;' : 'display: none;'}">
          <div class="sarathi-composer-bar">
            <textarea id="sarathi-input" class="sarathi-textarea" placeholder="Ask anything about our solutions..." rows="1"></textarea>
            <button class="sarathi-send-btn" id="sarathi-btn-send" aria-label="Send Message" disabled>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
              </svg>
            </button>
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
    const btnFullscreen = document.getElementById('sarathi-btn-fullscreen');
    const btnReset = document.getElementById('sarathi-btn-reset');
    const welcomeScreen = document.getElementById('sarathi-welcome-screen');
    const messagesList = document.getElementById('sarathi-messages-list');
    const typingWrap = document.getElementById('sarathi-typing-wrap');
    const textarea = document.getElementById('sarathi-input');
    const btnSend = document.getElementById('sarathi-btn-send');
    const gateView = document.getElementById('sarathi-gate-view');
    const gateName = document.getElementById('sarathi-gate-name');
    const gateContact = document.getElementById('sarathi-gate-contact');
    const gateError = document.getElementById('sarathi-gate-error');
    const gateSubmit = document.getElementById('sarathi-gate-submit');
    const composerContainer = document.getElementById('sarathi-composer-container');
    const btnTheme = document.getElementById('sarathi-btn-theme');

    function applyTheme(newTheme) {
      state.theme = newTheme;
      const rootEl = document.getElementById('sarathi-ai-root');
      if (rootEl) rootEl.setAttribute('data-theme', newTheme);
      if (chatWindow) chatWindow.setAttribute('data-theme', newTheme);
      localStorage.setItem(CONFIG.storageKeyTheme, newTheme);
      
      if (btnTheme) {
        btnTheme.innerHTML = newTheme === 'dark' ? 
          `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>` : 
          `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>`;
      }
    }

    if (btnTheme) {
      btnTheme.addEventListener('click', () => {
        applyTheme(state.theme === 'dark' ? 'light' : 'dark');
      });
    }

    function checkGateState() {
      const isLeadComplete = Boolean(state.leadInfo && state.leadInfo.name && state.leadInfo.contact);
      if (isLeadComplete) {
        if (gateView) gateView.style.display = 'none';
        if (composerContainer) composerContainer.style.display = 'flex';
        
        const hubTitle = document.getElementById('sarathi-hub-title');
        if (hubTitle && state.leadInfo.name) {
          const firstName = state.leadInfo.name.split(' ')[0];
          hubTitle.innerHTML = `Hi <span class="sarathi-gradient-name">${firstName}</span> 👋`;
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
      const contact = gateContact ? gateContact.value.trim() : '';

      if (!name || !contact) {
        if (gateError) {
          gateError.style.display = 'flex';
          gateError.classList.add('shake');
          setTimeout(() => gateError.classList.remove('shake'), 500);
        }
        return;
      }

      if (gateError) gateError.style.display = 'none';

      state.leadInfo = { 
        name, 
        contact, 
        phone: contact,
        interest: state.selectedInterest || '',
        timestamp: new Date().toISOString() 
      };
      localStorage.setItem(CONFIG.storageKeyLead, JSON.stringify(state.leadInfo));

      // Post lead to webhook
      if (CONFIG.leadEndpoint) {
        const leadPayload = {
          name: name,
          phone: contact,
          contact: contact,
          interest: state.selectedInterest || '',
          visitor_id: state.visitorId,
          conversation_id: state.conversationId,
          page_url: window.location.href,
          page_title: document.title,
          timestamp: new Date().toISOString()
        };

        fetch(CONFIG.leadEndpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(leadPayload)
        }).catch(err => {
          console.warn('[Sarathi AI] Lead capture webhook error:', err);
        });
      }

      checkGateState();
      playHapticTone('receive');

      if (state.selectedInterest && state.history.length === 0) {
        setTimeout(() => {
          handleSendMessage(`Hello! I'm interested in ${state.selectedInterest}. Can you share more details and solutions?`);
        }, 300);
      }
    }

    if (gateSubmit) {
      gateSubmit.addEventListener('click', handleGateSubmission);
    }

    if (gateName && gateContact) {
      [gateName, gateContact].forEach(input => {
        input.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            handleGateSubmission();
          }
        });
      });
    }

    // Toggle Chat Window
    function toggleChat(force) {
      state.isOpen = typeof force === 'boolean' ? force : !state.isOpen;
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
        if (launcherPill && (!state.leadInfo || !state.leadInfo.name)) {
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
        localStorage.setItem(CONFIG.storageKeyPos, JSON.stringify({ right: r, bottom: b }));
      },
      null,
      null
    );

    makeDraggable(
      chatWindow,
      function(r, b) {
        launcherWrap.style.right = r + 'px';
        launcherWrap.style.bottom = b + 'px';
        localStorage.setItem(CONFIG.storageKeyPos, JSON.stringify({ right: r, bottom: b }));
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

    if (btnClose) {
      const handleClose = (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleChat(false);
      };
      btnClose.addEventListener('click', handleClose);
      btnClose.addEventListener('pointerdown', (e) => e.stopPropagation());
    }

    if (btnFullscreen) {
      const handleFullscreen = (e) => {
        e.preventDefault();
        e.stopPropagation();
        window.open(CONFIG.fullscreenUrl, '_blank');
      };
      btnFullscreen.addEventListener('click', handleFullscreen);
      btnFullscreen.addEventListener('pointerdown', (e) => e.stopPropagation());
    }

    if (btnReset) {
      const handleReset = (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (confirm('Reset conversation history and start fresh?')) {
          state.history = [];
          saveChatHistory();
          checkGateState();
          showToast('Chat history cleared');
        }
      };
      btnReset.addEventListener('click', handleReset);
      btnReset.addEventListener('pointerdown', (e) => e.stopPropagation());
    }

    // Topic Card Click Handlers
    document.querySelectorAll('.sarathi-topic-card').forEach(card => {
      card.addEventListener('click', (e) => {
        const topic = card.getAttribute('data-topic');
        let prompt = '';
        
        if (topic) {
          if (topic.includes('Agentic AI')) {
            prompt = 'Tell me about Sarathi Agentic AI and Custom Solutions';
          } else if (topic.includes('Web Development')) {
            prompt = 'What Web Development & Cloud services do you provide?';
          } else if (topic.includes('Test Automation')) {
            prompt = 'How does Sarathi AI help with Test Automation and QA frameworks?';
          } else if (topic.includes('Professional Courses') || topic.includes('Training') || topic.includes('Courses') || topic.includes('Bootcamp')) {
            prompt = 'What Professional Courses and Training programs do you offer?';
          } else {
            prompt = `Tell me more about ${topic}`;
          }
        }

        if (prompt) {
          handleSendMessage(prompt);
        }
      });
    });

    // Textarea Auto-expand & Send Handling
    textarea.addEventListener('input', () => {
      textarea.style.height = 'auto';
      textarea.style.height = Math.min(textarea.scrollHeight, 90) + 'px';
      btnSend.disabled = !textarea.value.trim();
    });

    textarea.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        if (textarea.value.trim()) {
          handleSendMessage(textarea.value.trim());
          textarea.value = '';
          textarea.style.height = 'auto';
          btnSend.disabled = true;
        }
      }
    });

    btnSend.addEventListener('click', () => {
      if (textarea.value.trim()) {
        handleSendMessage(textarea.value.trim());
        textarea.value = '';
        textarea.style.height = 'auto';
        btnSend.disabled = true;
      }
    });

    // Handle Sending Message
    async function handleSendMessage(userText) {
      if (!userText || state.isTyping) return;

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

      // Show typing indicator
      state.isTyping = true;
      typingWrap.style.display = 'block';
      scrollToLatestExchange(true);

      // Send to Webhook or Intelligent Engine
      try {
        let aiResult = null;
        if (CONFIG.apiEndpoint) {
          const payload = {
            message: userText,
            visitor_id: state.visitorId,
            conversation_id: state.conversationId,
            name: (state.leadInfo && state.leadInfo.name) || '',
            phone: (state.leadInfo && (state.leadInfo.phone || state.leadInfo.contact)) || '',
            contact: (state.leadInfo && (state.leadInfo.phone || state.leadInfo.contact)) || '',
            page_url: window.location.href,
            page_title: document.title,
            timestamp: new Date().toISOString()
          };

          const response = await fetch(CONFIG.apiEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });

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
                data = { text: rawText };
              }
            }

            const item = Array.isArray(data) ? data[0] : data;
            let textContent = '';
            if (typeof item === 'string') {
              textContent = item;
            } else if (item && typeof item === 'object') {
              textContent = item.output || item.response || item.answer || item.text || item.message || item.content || '';
              if (!textContent && Object.keys(item).length > 0) {
                textContent = JSON.stringify(item);
              }
            }

            if (textContent) {
              aiResult = {
                category: (item && item.category) || 'AI Response',
                text: textContent,
                sources: (item && item.sources) || [],
                chips: (item && item.chips) || []
              };
            }
          }
        }

        if (!aiResult) {
          await new Promise(r => setTimeout(r, 600));
          aiResult = generateFallbackResponse(userText);
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
          chips: fallback.chips,
          time: formatTime(new Date()),
          feedback: null
        });
        saveChatHistory();
        playHapticTone('receive');
      } finally {
        state.isTyping = false;
        typingWrap.style.display = 'none';
        renderChatHistory();
        scrollToLatestExchange(true);
      }
    }

    // Render Conversation Stream
    function renderChatHistory() {
      const isLeadComplete = Boolean(state.leadInfo && state.leadInfo.name && state.leadInfo.contact);
      if (!isLeadComplete) {
        if (gateView) gateView.style.display = 'flex';
        if (welcomeScreen) welcomeScreen.style.display = 'none';
        if (messagesList) messagesList.style.display = 'none';
        if (composerContainer) composerContainer.style.display = 'none';
        return;
      }

      if (gateView) gateView.style.display = 'none';
      if (composerContainer) composerContainer.style.display = 'flex';

      if (state.history.length === 0) {
        welcomeScreen.style.display = 'flex';
        messagesList.style.display = 'none';
        return;
      }

      welcomeScreen.style.display = 'none';
      if (gateView) gateView.style.display = 'none';
      messagesList.style.display = 'flex';
      messagesList.innerHTML = '';

      state.history.forEach((msg, idx) => {
        const isBot = msg.sender === 'assistant';
        const msgEl = document.createElement('div');
        msgEl.className = `sarathi-message ${isBot ? 'assistant' : 'user'}`;
        msgEl.id = msg.id;

        let sourcesHtml = '';
        if (msg.sources && msg.sources.length > 0) {
          sourcesHtml = `<div class="sarathi-sources-list">`;
          msg.sources.forEach(src => {
            sourcesHtml += `
              <a class="sarathi-source-card" href="${src.url}" target="_blank">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>
                <span>${src.title}</span>
              </a>
            `;
          });
          sourcesHtml += `</div>`;
        }

        let chipsHtml = '';
        if (isBot && idx === state.history.length - 1 && msg.chips && msg.chips.length > 0) {
          chipsHtml = `<div class="sarathi-chips-row">`;
          msg.chips.forEach(chip => {
            chipsHtml += `
              <button class="sarathi-chip-btn">
                <span class="chip-spark">✦</span>
                <span class="chip-label">${chip}</span>
                <span class="chip-arrow">→</span>
              </button>
            `;
          });
          chipsHtml += `</div>`;
        }

        let actionsToolbarHtml = '';
        if (isBot) {
          actionsToolbarHtml = `
            <div class="sarathi-msg-actions-bar">
              <button class="sarathi-action-mini-btn btn-copy-msg" data-msg-id="${msg.id}" title="Copy response">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                <span>Copy</span>
              </button>
              <button class="sarathi-action-mini-btn btn-speak-msg" data-msg-id="${msg.id}" title="Read aloud">
                <span class="sarathi-sound-wave" style="display: none;">
                  <span></span><span></span><span></span>
                </span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                  <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                </svg>
                <span>Listen</span>
              </button>
              <div class="sarathi-feedback-group">
                <button class="sarathi-feedback-btn ${msg.feedback === 'up' ? 'active' : ''}" data-action="up" data-msg-id="${msg.id}" title="Helpful response">👍</button>
                <button class="sarathi-feedback-btn ${msg.feedback === 'down' ? 'active' : ''}" data-action="down" data-msg-id="${msg.id}" title="Not helpful">👎</button>
              </div>
            </div>
          `;
        } else {
          actionsToolbarHtml = `
            <div class="sarathi-user-actions-bar">
              <button class="sarathi-user-mini-btn btn-edit-msg" data-msg-id="${msg.id}" title="Edit question">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                <span>Edit</span>
              </button>
              <button class="sarathi-user-mini-btn btn-copy-msg" data-msg-id="${msg.id}" title="Copy question">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
              </button>
            </div>
          `;
        }

        const firstName = (state.leadInfo && state.leadInfo.name) ? state.leadInfo.name.split(' ')[0] : 'You';

        msgEl.innerHTML = `
          ${isBot ? `
            <div class="sarathi-msg-avatar">
              <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="17" r="5" fill="#38BDF8"/>
                <rect x="47.5" y="21" width="5" height="7" rx="2.5" fill="#FFFFFF"/>
                <rect x="18" y="44" width="7" height="18" rx="3.5" fill="#FFFFFF"/>
                <rect x="75" y="44" width="7" height="18" rx="3.5" fill="#FFFFFF"/>
                <rect x="23" y="27" width="54" height="49" rx="19" fill="#FFFFFF"/>
                <rect x="29" y="33" width="42" height="37" rx="13" fill="#0F172A"/>
                <circle cx="41.5" cy="49" r="4.5" fill="#38BDF8"/>
                <circle cx="58.5" cy="49" r="4.5" fill="#38BDF8"/>
                <path d="M 44.5 56.5 Q 50 62 55.5 56.5" stroke="#38BDF8" stroke-width="2.5" stroke-linecap="round" fill="none"/>
              </svg>
            </div>
          ` : `
            <div class="sarathi-user-avatar">
              <span>${firstName.charAt(0).toUpperCase()}</span>
            </div>
          `}
          <div class="sarathi-msg-content">
            <div class="sarathi-msg-header-pill">
              <span class="sarathi-msg-sender-name">${isBot ? '✦ Sarathi Neural AI' : firstName}</span>
              <span class="sarathi-msg-header-time">${msg.time}</span>
            </div>

            <div class="sarathi-msg-bubble">
              ${isBot && msg.category ? `<div class="sarathi-msg-category-tag"><span class="tag-spark">✦</span> ${msg.category}</div>` : ''}
              <div class="sarathi-markdown">${isBot ? renderMarkdown(msg.text) : escapeHtml(msg.text)}</div>
              ${sourcesHtml}
            </div>

            <div class="sarathi-msg-time-row">
              <span class="sarathi-msg-footer-label">${isBot ? 'Verified Response' : 'Sent'}</span>
              ${actionsToolbarHtml}
            </div>
            ${chipsHtml}
          </div>
        `;

        // Listeners for Code Copy Buttons
        msgEl.querySelectorAll('.sarathi-code-copy-btn').forEach(btn => {
          btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const codeId = btn.getAttribute('data-code-id');
            const codeEl = document.getElementById(codeId);
            if (codeEl) {
              navigator.clipboard.writeText(codeEl.textContent).then(() => {
                btn.classList.add('copied');
                btn.querySelector('span').textContent = 'Copied!';
                showToast('Code copied to clipboard ✨');
                setTimeout(() => {
                  btn.classList.remove('copied');
                  btn.querySelector('span').textContent = 'Copy';
                }, 2000);
              });
            }
          });
        });

        // Listeners for Message Copy Button (User or Bot)
        msgEl.querySelectorAll('.btn-copy-msg').forEach(btn => {
          btn.addEventListener('click', () => {
            navigator.clipboard.writeText(msg.text).then(() => {
              btn.classList.add('copied');
              const span = btn.querySelector('span');
              const orig = span ? span.textContent : 'Copy';
              if (span) span.textContent = 'Copied!';
              showToast('Copied to clipboard ✨');
              setTimeout(() => {
                btn.classList.remove('copied');
                if (span) span.textContent = orig;
              }, 2000);
            });
          });
        });

        // Listeners for Edit / Re-ask Button on User questions
        msgEl.querySelectorAll('.btn-edit-msg').forEach(btn => {
          btn.addEventListener('click', () => {
            if (textarea) {
              textarea.value = msg.text;
              textarea.focus();
              textarea.style.height = 'auto';
              textarea.style.height = Math.min(textarea.scrollHeight, 90) + 'px';
              if (btnSend) btnSend.disabled = false;
              showToast('Question loaded into composer ✍️');
            }
          });
        });

        // Listeners for TTS Listen Button
        msgEl.querySelectorAll('.btn-speak-msg').forEach(btn => {
          btn.addEventListener('click', () => {
            const isSpeaking = state.speakingId === msg.id;
            const soundWave = btn.querySelector('.sarathi-sound-wave');
            const iconSvg = btn.querySelector('svg');
            const labelSpan = btn.querySelector('span:not(.sarathi-sound-wave)');

            speakMessage(msg.text, msg.id, () => {
              btn.classList.remove('speaking');
              if (soundWave) soundWave.style.display = 'none';
              if (iconSvg) iconSvg.style.display = 'inline-block';
              if (labelSpan) labelSpan.textContent = 'Listen';
            });

            if (!isSpeaking) {
              btn.classList.add('speaking');
              if (soundWave) soundWave.style.display = 'inline-flex';
              if (iconSvg) iconSvg.style.display = 'none';
              if (labelSpan) labelSpan.textContent = 'Pause';
            } else {
              btn.classList.remove('speaking');
              if (soundWave) soundWave.style.display = 'none';
              if (iconSvg) iconSvg.style.display = 'inline-block';
              if (labelSpan) labelSpan.textContent = 'Listen';
            }
          });
        });

        // Feedback Buttons (Thumbs Up / Down)
        msgEl.querySelectorAll('.sarathi-feedback-btn').forEach(fBtn => {
          fBtn.addEventListener('click', () => {
            const action = fBtn.getAttribute('data-action');
            msg.feedback = msg.feedback === action ? null : action;
            saveChatHistory();
            renderChatHistory();
            if (msg.feedback) {
              showToast(msg.feedback === 'up' ? 'Glad this helped! 🌟' : 'Thanks! We will improve our answers.');
            }
          });
        });

        // Flow Chips Listeners
        msgEl.querySelectorAll('.sarathi-chip-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            const label = btn.querySelector('.chip-label') ? btn.querySelector('.chip-label').textContent : btn.textContent;
            handleSendMessage(label.trim());
          });
        });

        messagesList.appendChild(msgEl);
      });

      scrollToLatestExchange(false);
    }

    function scrollToLatestExchange(smooth = false) {
      const body = document.getElementById('sarathi-body');
      if (!body) return;

      setTimeout(() => {
        const userMessages = document.querySelectorAll('.sarathi-message.user');
        if (userMessages.length === 0) {
          body.scrollTop = 0;
          return;
        }

        // For the first user message or initial exchange, keep scroll firmly at 0px
        if (userMessages.length === 1 || state.history.length <= 2) {
          body.scrollTop = 0;
          return;
        }

        // For subsequent questions, place the question 16px below the header
        const lastUserMsg = userMessages[userMessages.length - 1];
        let topOffset = 0;
        let curr = lastUserMsg;
        while (curr && curr !== body) {
          topOffset += curr.offsetTop;
          curr = curr.offsetParent;
        }

        const targetTop = Math.max(0, topOffset - 16);
        if (smooth) {
          body.scrollTo({
            top: targetTop,
            behavior: 'smooth'
          });
        } else {
          body.scrollTop = targetTop;
        }
      }, 30);
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
      state.history = getChatHistory();
      renderChatHistory();
    });

    // Render Initial State
    if (state.history.length > 0) {
      renderChatHistory();
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
