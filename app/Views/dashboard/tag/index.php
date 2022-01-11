<a href="/dashboard/tag/new" > Crear</a>

<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Opciones</th>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($tags as $key =>$t) : ?>
            <tr>
                <td><?=$t->id ?></td>
                <td><?=$t->name ?></td>
                <td>

                    <form action="/dashboard/tag/delete/<?=$t->id ?>" method="POST">
                        <button>Eliminar</button>
                    </form>

                    <a href="/dashboard/tag/<?=$t->id ?>/edit">Editar</a>

                </td>
            </tr>
        <?php endforeach ?>



    </tbody>
</table>

<?= $pager->links() ?>