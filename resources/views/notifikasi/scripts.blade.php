{{-- resources/views/notifikasi/scripts.blade.php --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const notifications = document.querySelectorAll('.notification-item');

        // Tab filtering functionality
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active tab
                tabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Scroll active tab into view (untuk mobile)
                const tabsContainer = document.querySelector('.filter-tabs');
                const activeBtn = this;

                if (tabsContainer && activeBtn) {
                    setTimeout(() => {
                        const btnRect = activeBtn.getBoundingClientRect();
                        const containerRect = tabsContainer.getBoundingClientRect();

                        if (btnRect.left < containerRect.left) {
                            activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
                        } else if (btnRect.right > containerRect.right) {
                            activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'end' });
                        }
                    }, 100);
                }

                // Filter notifications
                const filter = this.dataset.filter;
                let visibleCount = 0;

                notifications.forEach(notification => {
                    if (filter === 'all') {
                        notification.style.display = 'block';
                        visibleCount++;
                    } else {
                        if (notification.dataset.type === filter) {
                            notification.style.display = 'block';
                            visibleCount++;
                        } else {
                            notification.style.display = 'none';
                        }
                    }
                });

                // Show empty state jika tidak ada notifikasi
                const listContainer = document.querySelector('.notifications-list');
                const emptyState = listContainer.querySelector('.empty-state');

                if (visibleCount === 0 && !emptyState) {
                    const noDataDiv = document.createElement('div');
                    noDataDiv.className = 'empty-state';
                    noDataDiv.innerHTML = `
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3>Tidak ada notifikasi</h3>
                        <p>Tidak ada notifikasi untuk kategori ini</p>
                    `;
                    listContainer.appendChild(noDataDiv);
                } else if (visibleCount > 0 && emptyState) {
                    emptyState.remove();
                }
            });
        });

        // Touch feedback untuk cards
        const notificationCards = document.querySelectorAll('.notification-card');
        notificationCards.forEach(card => {
            card.addEventListener('touchstart', function(e) {
                this.style.background = '#f5f5f5';
            });

            card.addEventListener('touchend', function(e) {
                this.style.background = '';
            });

            card.addEventListener('mouseenter', function(e) {
                if (!isTouchDevice()) {
                    this.style.background = '#fafafa';
                }
            });

            card.addEventListener('mouseleave', function(e) {
                this.style.background = '';
            });
        });

        // Auto-scroll to first tab on load
        const firstTab = document.querySelector('.tab-btn.active');
        if (firstTab && document.querySelector('.filter-tabs')) {
            setTimeout(() => {
                firstTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
            }, 300);
        }
    });

    // Mark as read functionality
    function markAsRead(id) {
        fetch(`/notifikasi/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                const item = document.querySelector(`.notification-item[data-id="${id}"]`);
                if (item) {
                    item.classList.remove('unread');
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Helper function to detect touch device
    function isTouchDevice() {
        return (
            (typeof window !== 'undefined' &&
                ('ontouchstart' in window ||
                    navigator.maxTouchPoints > 0 ||
                    navigator.msMaxTouchPoints > 0))
        );
    }

    // Handle viewport changes for responsive behavior
    let viewportWidth = window.innerWidth;
    window.addEventListener('orientationchange', function() {
        const newWidth = window.innerWidth;
        if (newWidth !== viewportWidth) {
            viewportWidth = newWidth;
            // Recalculate layouts if needed
            const tabsContainer = document.querySelector('.filter-tabs');
            if (tabsContainer) {
                const activeBtn = document.querySelector('.tab-btn.active');
                if (activeBtn) {
                    activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            }
        }
    });

    // Debounced resize handler
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const activeBtn = document.querySelector('.tab-btn.active');
            if (activeBtn && document.querySelector('.filter-tabs')) {
                activeBtn.scrollIntoView({ behavior: 'auto', block: 'nearest', inline: 'center' });
            }
        }, 250);
    });
</script>
