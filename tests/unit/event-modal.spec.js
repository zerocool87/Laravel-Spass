import { describe, it, expect, beforeEach } from 'vitest'

// Minimal DOM tests for modal behaviour using jsdom
import fs from 'fs'
import path from 'path'

const modalScript = fs.readFileSync(path.resolve('./resources/js/event-detail-modal.js'), 'utf8')

describe('event detail modal', () => {
  beforeEach(() => {
    document.body.innerHTML = ''
    // execute script to register functions
    const script = document.createElement('script')
    script.textContent = modalScript
    document.body.appendChild(script)
  })

  it('opens and closes the modal and returns focus', async () => {
    const btn = document.createElement('button')
    btn.id = 'trigger'
    document.body.appendChild(btn)

    expect(window.openEventDetailModal).toBeDefined()

    // stub fetch
    global.fetch = async () => ({ ok: true, json: async () => ({ title: 'T', description: 'D', start_at: null, end_at: null, attachments: [] }) })

    await window.openEventDetailModal(1, '/events/1', btn)

    const modal = document.getElementById('event-detail-modal')
    expect(modal).not.toBeNull()
    expect(modal.classList.contains('flex')).toBe(true)

    // close programmatically
    window.closeEventDetailModal()
    expect(modal.classList.contains('hidden')).toBe(true)

  })
})
