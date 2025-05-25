<!-- Модальное окно создания новой папки -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderModalLabel">Создать новую папку</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-folder-form" action="<?php echo e(route('user.folders.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folder-name" class="form-label">Название папки</label>
                        <input type="text" class="form-control" id="folder-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="folder-color" class="form-label">Цвет папки</label>
                        <select class="form-select" id="folder-color" name="color">
                            <option value="primary">Синий</option>
                            <option value="success">Зеленый</option>
                            <option value="danger">Красный</option>
                            <option value="warning">Желтый</option>
                            <option value="info">Голубой</option>
                            <option value="dark">Черный</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Создать</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно для подтверждения удаления папки -->
<div class="modal fade" id="deleteFolderModal" tabindex="-1" aria-labelledby="deleteFolderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteFolderModalLabel">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>Удаление папки
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fa-solid fa-folder-open text-danger fa-3x"></i>
                </div>
                <p>Вы действительно хотите удалить папку <strong id="folderNameToDelete" class="text-danger">-</strong>?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <form id="delete-folder-form" action="" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash me-2"></i>Удалить папку
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast-уведомление для подтверждения копирования -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
    <div id="copyToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-check-circle me-2"></i>
                <span id="toastMessage">Ссылка скопирована в буфер обмена</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Закрыть"></button>
        </div>
    </div>
</div>
<?php /**PATH C:\OSPanel\domains\sert\resources\views/user/certificates/partials/_modals.blade.php ENDPATH**/ ?>