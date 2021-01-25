const imageTags = document.querySelectorAll("img");

function appendOnError(images) {
    images.forEach(img => img.onerror = function() {
        this.onerror=null;
        this.src='/public/uploads/placeholder.png';
    })
}

appendOnError(imageTags);
