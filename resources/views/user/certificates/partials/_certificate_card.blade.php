<div class="card border-0 rounded-4 shadow-sm h-100 certificate-card"
    data-certificate-id="{{ $certificate->id }}"
    data-public-url="{{ route('certificates.public', $certificate->uuid) }}"
    data-certificate-number="{{ $certificate->certificate_number }}">
    <!-- Используем загруженную обложку в качестве главного изображения карточки -->
    <div class="certificate-cover-wrapper">
        <img src="{{ $certificate->cover_image_url }}" class="certificate-cover-image" alt="Обложка документа">
        <div class="certificate-status-badge">
            @if ($certificate->status == 'active')
                <span class="badge bg-success">Активен</span>
            @elseif ($certificate->status == 'used')
                <span class="badge bg-secondary">Использован</span>
            @elseif ($certificate->status == 'expired')
                <span class="badge bg-warning">Истек</span>
            @elseif ($certificate->status == 'canceled')
                <span class="badge bg-danger">Отменен</span>
            @endif
        </div>
        
        <!-- Добавляем отметку времени -->
       
           
       
        
   
    </div>
 
    <!-- Действия с документом -->
    <div class="certificate-actions">
        <a href="{{ route('certificates.public', $certificate->uuid) }}" class="btn btn-primary btn-sm" target="_blank">
            <i class="fa-solid fa-external-link-alt me-1" style="margin:0 !important"></i>
        </a>
     
      
    </div>
</div>

