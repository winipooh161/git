<div class="col-12">
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-body p-4 text-center">
            <div class="d-flex flex-column align-items-center py-4">
                <i class="fa-solid fa-certificate text-muted fa-3x mb-3"></i>
                <h5 class="fs-5 mb-2">У вас нет документов</h5>
                <p class="text-muted mb-0">Здесь будут отображаться полученные вами подарочные документы</p>
                
                @if(!Auth::user()->hasRole('predprinimatel'))
                <div class="alert alert-info mt-3 w-75">
                    <i class="fa-solid fa-lightbulb me-2"></i>
                    <strong>Совет:</strong> Вы также можете переключиться на режим предпринимателя для создания собственных документов
                    <form action="{{ route('role.switch') }}" method="POST" class="mt-2">
                        @csrf
                        <input type="hidden" name="role" value="predprinimatel">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-briefcase me-2"></i>Стать предпринимателем
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
