<?php

use App\Database\Table\UserTable;
use App\Modal\ConfirmModal;

?>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col" style="width:2%">#</th>
                <th scope="col" style="width:5%">Date</th>
                <th scope="col" style="width:8%">Payé par</th>
                <th scope="col" style="width:5%">Montant</th>
                <th scope="col" style="width:18%">Notes</th>
                <th scope="col" style="width:5%">Options</th>
                <th scope="col" style="width:12%">Dernière modif.</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($expenses) === 0): ?>
                <tr><td colspan=7 class="text-center">Aucune dépense</td></tr>
            <?php else: ?>
                <?php $i = count($expenses) ?>
                <?php foreach ($expenses as $expense): ?>
                    <tr valign="middle">
                        <td class="text-muted small"><?= $i ?></td>
                        <td><?= $expense->getSpentAt('d/m/Y') ?></td>
                        <td><?= $expense->getName() ?></td>
                        <td><?= $expense->getAmount() ?></td>
                        <td class="small"><?= $expense->getNotes() ?></td>
                        <td>
                            <?php $params = ['slug' => $project->getSlug(), 'id' => $project->getID(), 'expense_id' => $expense->getID()] ?>
                            <a href="<?= $router->url('edit-expense', $params) ?>" class="btn-icon btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></a>
                            <?php if( $expense->getAuthorID() === $_SESSION['UserModel']->getID() ): // Only author can edit or delete ?>
                                <?php ( new ConfirmModal( 'delete-expense-row'.$i, $router->url('delete-expense', $params) ) )
                                    ->showTrigger('<i class="fas fa-trash"></i>', 'button', 'btn-icon btn btn-sm btn-danger') ?>
                            <?php endif ?>
                        </td>
                        <?php if(@$expense->getUpdatedBy() !== null && @$expense->getUpdatedAt() !== null): ?>
                            <td class="text-muted small">
                                <?= (new UserTable)->getOneById($expense->getUpdatedBy(), 'firstname, lastname')->getName() ?>, le <?= $expense->getUpdatedAt('d/m/Y') ?>
                            </td>
                        <?php else: ?>
                            <td class="text-muted small"></td>
                        <?php endif ?>
                    </tr>
                    <?php $i-- ?>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</div>