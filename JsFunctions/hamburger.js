document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('hamburger').addEventListener('click', function () {
        document.getElementById('mainNav').classList.toggle('show');
    });

    // for categories to redirect
    document.getElementById('categorySelect').addEventListener('change', function () {
        var url = this.value;
        if (url) {
            window.location.href = url;
        }
    });


});