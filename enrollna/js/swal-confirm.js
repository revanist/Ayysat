document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-swal-confirm]');
    if (!form || form.dataset.swalConfirmed === 'true') return;

    event.preventDefault();
    Swal.fire({
        icon: form.dataset.swalIcon || 'warning',
        title: form.dataset.swalTitle || 'Are you sure?',
        text: form.dataset.swalConfirm,
        showCancelButton: true,
        confirmButtonText: 'Continue',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0d3b66'
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.swalConfirmed = 'true';
            form.requestSubmit();
        }
    });
});

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[data-swal-confirm]');
    if (!link) return;

    event.preventDefault();
    Swal.fire({
        icon: 'warning',
        title: link.dataset.swalTitle || 'Are you sure?',
        text: link.dataset.swalConfirm,
        showCancelButton: true,
        confirmButtonText: 'Continue',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0d3b66'
    }).then((result) => {
        if (result.isConfirmed) window.location.assign(link.href);
    });
});
