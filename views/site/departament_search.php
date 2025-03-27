<div class="search-container">
    <h2>Поиск сотрудников по кафедре</h2>

    <form method="GET" action="/departament_search" class="search-form">
        <div class="form-group">
            <label for="departament_id">Выберите кафедру:</label>
            <select name="departament_id" id="departament_id" class="form-control" required>
                <option value="">-- Все кафедры --</option>
                <?php foreach ($departaments as $departament): ?>
                    <option
                            value="<?= $departament->id ?>"
                        <?= ($selectedDepartament == $departament->id) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($departament->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Найти
        </button>
    </form>

    <?php if ($employees->isNotEmpty()): ?>
        <div class="results-container">
            <h3>Результаты поиска:</h3>
            <div class="employee-cards">
                <?php foreach ($employees as $employee): ?>
                    <div class="employee-card">
                        <div class="employee-info">
                            <strong>ФИО:</strong>
                            <?= htmlspecialchars($employee->last_name) ?>
                            <?= htmlspecialchars($employee->first_name) ?>
                            <?= htmlspecialchars($employee->middle_name ?? '') ?>
                        </div>
                        <div class="employee-info">
                            <strong>ID пользователя:</strong> <?= $employee->user_id ?>
                        </div>
                        <div class="employee-info">
                            <strong>Должность:</strong> <?= htmlspecialchars($employee->post) ?>
                        </div>
                        <div class="employee-info">
                            <strong>Кафедра:</strong>
                            <?= $employee->departament
                                ? htmlspecialchars($employee->departament->name)
                                : 'Не назначена'
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif ($selectedDepartament): ?>
        <div class="alert alert-info">
            На выбранной кафедре нет сотрудников.
        </div>
    <?php endif; ?>
</div>