<div>
    <h2>Поиск дисциплин сотрудника</h2>
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/employee_search">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Выберите сотрудника:</label>
                            <select name="employees_id" class="form-control" required>
                                <option value="">-- Выберите сотрудника --</option>
                                <?php foreach ($employees as $employee): ?>
                                    <option value="<?= $employee->id ?>"
                                        <?= ($request->get('employees_id', '') == $employee->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($employee->last_name) ?>
                                        <?= htmlspecialchars($employee->first_name) ?>
                                        <?= htmlspecialchars($employee->middle_name ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Найти
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($results)): ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4>Результаты поиска</h4>
            </div>
            <div class="card-body">
                <h5>Сотрудник:
                    <?= htmlspecialchars($results['employee']->last_name) ?>
                    <?= htmlspecialchars($results['employee']->first_name) ?>
                </h5>

                <?php if ($results['subjects']->isNotEmpty()): ?>
                    <table class="table table-striped mt-3">
                        <thead>
                        <tr>
                            <th>Дисциплина</th>
                            <th>Часы</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results['subjects'] as $subject): ?>
                            <tr>
                                <td><?= htmlspecialchars($subject->name) ?></td>
                                <td><?= $subject->pivot->hours ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning mt-3">У сотрудника нет дисциплин</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>