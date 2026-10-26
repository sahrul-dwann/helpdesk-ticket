
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('ticketTableWrapper');
    const scrollbar = document.getElementById('ticketScrollbar');
    const thumb = document.getElementById('ticketScrollbarThumb');

    if (!wrapper || !scrollbar || !thumb) {
        return;
    }

    let isDragging = false;
    let startX = 0;

    function updateScrollbar() {
        const visibleWidth = wrapper.clientWidth;
        const fullWidth = wrapper.scrollWidth;
        const trackWidth = scrollbar.clientWidth;

        if (fullWidth <= visibleWidth) {
            scrollbar.style.display = 'none';
            return;
        }

        scrollbar.style.display = 'block';

        const thumbWidth = Math.max(
            30,
            (visibleWidth / fullWidth) * trackWidth
        );

        thumb.style.width = thumbWidth + 'px';

        const maxScroll = fullWidth - visibleWidth;
        const maxThumbLeft = trackWidth - thumbWidth;

        const thumbLeft = maxScroll > 0
            ? (wrapper.scrollLeft / maxScroll) * maxThumbLeft
            : 0;

        thumb.style.left = thumbLeft + 'px';
    }

    function moveThumb(clientX) {
        const rect = scrollbar.getBoundingClientRect();
        const maxThumbLeft = scrollbar.clientWidth - thumb.offsetWidth;
        const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;

        let newLeft = clientX - rect.left - startX;

        newLeft = Math.max(0, Math.min(newLeft, maxThumbLeft));

        wrapper.scrollLeft = maxThumbLeft > 0
            ? (newLeft / maxThumbLeft) * maxScroll
            : 0;

        updateScrollbar();
    }

    thumb.addEventListener('pointerdown', function (event) {
        isDragging = true;

        startX = event.clientX - thumb.getBoundingClientRect().left;

        thumb.setPointerCapture(event.pointerId);
        event.preventDefault();
    });

    thumb.addEventListener('pointermove', function (event) {
        if (!isDragging) return;

        moveThumb(event.clientX);
    });

    thumb.addEventListener('pointerup', function () {
        isDragging = false;
    });

    thumb.addEventListener('pointercancel', function () {
        isDragging = false;
    });

    scrollbar.addEventListener('pointerdown', function (event) {
        if (event.target === thumb) return;

        const rect = scrollbar.getBoundingClientRect();
        const thumbWidth = thumb.offsetWidth;
        const maxThumbLeft = scrollbar.clientWidth - thumbWidth;
        const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;

        const clickPosition = event.clientX - rect.left;

        const newLeft = Math.max(
            0,
            Math.min(clickPosition - thumbWidth / 2, maxThumbLeft)
        );

        wrapper.scrollLeft = maxThumbLeft > 0
            ? (newLeft / maxThumbLeft) * maxScroll
            : 0;

        updateScrollbar();
    });

    wrapper.addEventListener('scroll', updateScrollbar);
    window.addEventListener('resize', updateScrollbar);

    updateScrollbar();
});
