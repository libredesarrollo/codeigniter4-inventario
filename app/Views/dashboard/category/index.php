<a href="/dashboard/category/new" > Crear</a>

<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Opciones</th>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($categories as $key => $c) : ?>
            <tr>
                <td><?= $c->id ?></td>
                <td><?= $c->name ?></td>
                <td>

                    <form action="/dashboard/category/delete/<?= $c->id ?>" method="POST">
                        <button>Eliminar</button>
                    </form>

                    <a href="/dashboard/category/<?= $c->id ?>/edit">Editar</a>

                </td>
            </tr>
        <?php endforeach ?>



    </tbody>
</table>

<?= $pager->links() ?>