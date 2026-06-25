/**
 * Lightweight canvas confetti for celebrations.
 * Trigger via window.confetti() or Alpine $store.confetti.fire().
 */
export default function initConfetti() {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    let particles = [];
    let animationId = null;
    let isRunning = false;

    canvas.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;pointer-events:none;z-index:9999';
    document.body.appendChild(canvas);

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    window.addEventListener('resize', resize);
    resize();

    const colors = ['#faa21b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#ef4444', '#14b8a6'];

    function createParticle(x, y) {
        return {
            x: x ?? Math.random() * canvas.width,
            y: y ?? -20,
            size: Math.random() * 10 + 5,
            color: colors[Math.floor(Math.random() * colors.length)],
            speedX: (Math.random() - 0.5) * 8,
            speedY: Math.random() * 6 + 4,
            rotation: Math.random() * 360,
            rotationSpeed: (Math.random() - 0.5) * 10,
            opacity: 1,
            shape: Math.random() > 0.5 ? 'rect' : 'circle',
            gravity: 0.15,
            drag: 0.98,
            wobble: Math.random() * 2,
            wobbleSpeed: Math.random() * 0.1 + 0.05,
        };
    }

    function fire(opts = {}) {
        const count = opts.particleCount || 80;
        const originX = opts.x ?? canvas.width / 2;
        const originY = opts.y ?? canvas.height / 2;

        for (let i = 0; i < count; i++) {
            particles.push(createParticle(
                originX + (Math.random() - 0.5) * 100,
                originY + (Math.random() - 0.5) * 50
            ));
        }

        if (!isRunning) {
            isRunning = true;
            animate();
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (let i = particles.length - 1; i >= 0; i--) {
            const p = particles[i];
            p.x += p.speedX;
            p.speedY += p.gravity;
            p.speedX *= p.drag;
            p.y += p.speedY;
            p.rotation += p.rotationSpeed;
            p.opacity -= 0.003;
            p.x += Math.sin(p.y * p.wobbleSpeed) * p.wobble;

            if (p.opacity <= 0 || p.y > canvas.height + 50) {
                particles.splice(i, 1);
                continue;
            }

            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate((p.rotation * Math.PI) / 180);
            ctx.globalAlpha = Math.max(0, p.opacity);
            ctx.fillStyle = p.color;

            if (p.shape === 'rect') {
                ctx.fillRect(-p.size / 2, -p.size / 4, p.size, p.size / 2);
            } else {
                ctx.beginPath();
                ctx.arc(0, 0, p.size / 2, 0, Math.PI * 2);
                ctx.fill();
            }

            ctx.restore();
        }

        if (particles.length > 0) {
            animationId = requestAnimationFrame(animate);
        } else {
            isRunning = false;
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    function clear() {
        particles = [];
        if (animationId) cancelAnimationFrame(animationId);
        isRunning = false;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    window.confetti = fire;

    return { fire, clear };
}
