<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <h1 class="mb-4">Выберите шаблон документа</h1>
    
    <!-- Информация о стиках -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <h5 class="mb-1">Информация о стиках</h5>
                <p class="mb-0">За каждый выпускаемый сертификат списывается 1 стик.</p>
                <p class="mb-0">При создании серии сертификатов, стики списываются сразу за все сертификаты.</p>
                <p class="mb-0">У вас доступно: <strong><?php echo e(Auth::user()->sticks); ?></strong> стиков.</p>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="<?php echo e(route('entrepreneur.certificates.select-template')); ?>" method="GET" id="filter-form">
                <div class="row g-3">
                    <!-- Фильтр по типу документа -->
                    <div class="col-md-4">
                        <label for="type" class="form-label">Тип документа</label>
                        <select class="form-select" id="type" name="type" onchange="this.form.submit()">
                            <option value="">Все типы</option>
                            <?php $__currentLoopData = $documentTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type); ?>" <?php echo e(request('type') == $type ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <!-- Фильтр по категории -->
                    <div class="col-md-4">
                        <label for="category" class="form-label">Категория</label>
                        <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                            <option value="">Все категории</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                                    <?php echo e($category->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <!-- Кнопка сброса фильтров -->
                    <div class="col-md-4 d-flex align-items-end">
                        <a href="<?php echo e(route('entrepreneur.certificates.select-template')); ?>" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-xmark me-1"></i> Сбросить фильтры
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="position-relative">
                        <img src="<?php echo e($template->image_url); ?>" class="card-img-top rounded-top" alt="<?php echo e($template->name); ?>">
                        
                        <?php if($template->is_premium): ?>
                            <span class="badge bg-warning position-absolute top-0 end-0 m-3">Премиум</span>
                        <?php endif; ?>
                        
                        <!-- Показываем тип документа -->
                        <span class="badge bg-info position-absolute top-0 start-0 m-3">
                            <?php echo e($template->document_type_label); ?>

                        </span>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo e($template->name); ?></h5>
                        
                        <!-- Показываем категорию -->
                        <p class="card-text small text-muted mb-2">
                            <i class="fa-solid fa-folder me-1"></i> <?php echo e($template->category->name ?? 'Без категории'); ?>

                        </p>
                        
                        <p class="card-text"><?php echo e(Str::limit($template->description, 100)); ?></p>
                        
                        <!-- Выравниваем кнопку по нижнему краю -->
                        <div class="mt-auto pt-3">
                            <a href="<?php echo e(route('entrepreneur.certificates.create', $template)); ?>" class="btn btn-primary w-100">
                                <i class="fa-solid fa-check me-2"></i>Выбрать шаблон
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <div class="display-6 text-muted">Шаблоны не найдены</div>
                <p class="mt-3">
                    <a href="<?php echo e(route('entrepreneur.certificates.select-template')); ?>" class="btn btn-outline-primary">
                        <i class="fa-solid fa-refresh me-2"></i>Сбросить фильтры
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lk', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\sert\resources\views/entrepreneur/certificates/select-template.blade.php ENDPATH**/ ?>