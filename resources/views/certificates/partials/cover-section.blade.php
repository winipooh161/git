<!-- Секция с обложкой -->
<div class="cover-section" id="coverSection">
    <div class="cover-container">
        <img class="cover-image" src="{{ asset('storage/' . $certificate->cover_image) }}" alt="Обложка документа">
        <div class="cover-overlay"></div>
    
        
        <div class="swipe-indicator" id="swipeIndicator" role="button" tabindex="0" aria-label="Прокрутить к содержимому">
            <i class="fa-solid fa-chevron-up"></i>
            <span class="mobile-text">Свайпните вверх</span>
            <span class="desktop-text">Прокрутите вниз</span>
        </div>
    </div>
</div>
