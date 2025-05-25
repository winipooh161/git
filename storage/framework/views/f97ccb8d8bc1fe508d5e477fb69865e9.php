<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-transparent border-0 pt-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold mb-0 fs-6">Параметры документа</h5>
        </div>
        
        <!-- Добавляем систему вкладок -->
        <ul class="nav nav-tabs card-header-tabs" id="certificateTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main-tab-content" 
                        type="button" role="tab" aria-controls="main-tab-content" aria-selected="true">
                    <i class="fa-solid fa-info-circle me-1"></i><span class="d-none d-md-inline">Основное</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="visual-tab" data-bs-toggle="tab" data-bs-target="#visual-tab-content" 
                        type="button" role="tab" aria-controls="visual-tab-content" aria-selected="false">
                    <i class="fa-solid fa-image me-1"></i><span class="d-none d-md-inline">Визуал</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="advanced-tab" data-bs-toggle="tab" data-bs-target="#advanced-tab-content" 
                        type="button" role="tab" aria-controls="advanced-tab-content" aria-selected="false">
                    <i class="fa-solid fa-sliders me-1"></i><span class="d-none d-md-inline">Ещё</span>
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('entrepreneur.certificates.store', $template)); ?>" id="desktopCertificateForm" class="desktop-form" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <!-- Контент вкладок -->
            <div class="tab-content" id="certificateTabsContent">
                <!-- Вкладка с основной информацией -->
                <div class="tab-pane fade show active" id="main-tab-content" role="tabpanel" aria-labelledby="main-tab">
                    <?php echo $__env->make('entrepreneur.certificates.partials.form_tabs.main_tab', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                
                <!-- Вкладка с визуальными настройками -->
                <div class="tab-pane fade" id="visual-tab-content" role="tabpanel" aria-labelledby="visual-tab">
                    <?php echo $__env->make('entrepreneur.certificates.partials.form_tabs.visual_tab', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                
                <!-- Вкладка с дополнительными настройками -->
                <div class="tab-pane fade" id="advanced-tab-content" role="tabpanel" aria-labelledby="advanced-tab">
                    <?php echo $__env->make('entrepreneur.certificates.partials.form_tabs.advanced_tab', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>

            <!-- Кнопки управления формой -->
            <div class="d-grid gap-1 gap-sm-2 mt-3 pt-2 border-top">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1 me-sm-2"></i>Создать 
                </button>
                <a href="<?php echo e(route('entrepreneur.certificates.select-template')); ?>" class="btn btn-outline-secondary btn-sm">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/partials/certificate_form.blade.php ENDPATH**/ ?>