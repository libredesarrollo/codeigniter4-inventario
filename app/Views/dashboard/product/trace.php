<p>
    Ventas y compras de <?= $product->name ?>
</p>


<ul>
    <li>
        Precio <?= $product->price ?>
    </li>
    <li>
        Última Entrada <?= $product->entry ?>
    </li>
    <li>
        Última Salida <?= $product->exit ?>
    </li>
</ul>

<h3>Filtro</h3>

<form method="get" id="formFilter">
    <select name="type">
        <option value="">Tipos</option>
        <option <?= ($typeId == "exit") ? "selected" : "" ?> value="exit">Salida</option>
        <option <?= ($typeId == "entry") ? "selected" : "" ?> value="entry">Entrada</option>
    </select>

    <select name="user_id">
        <option value="">Usuarios</option>
        <?php foreach ($users as $u) : ?>
            <option <?= ($u->id == $userId) ? "selected" : "" ?> value="<?= $u->id ?>"><?= $u->username ?></option>
        <?php endforeach ?>
    </select>

    <br>

    <h4>Busqueda</h4>

    <input value="<?= $search ?>" type="text" name="search" placeholder="Buscar">


    <h3>Cantidades</h3>
    <label for="check_cant">
        Activar
        <input type="checkbox" name="check_cant" id="check_cant" checked>
    </label>
    <br>
    <label for="min_cant">
        Minimo <span><?= $minCant ? $minCant : 0 ?></span>:
        <input type="range" name="min_cant" value="<?= $minCant ? $minCant : 0 ?>" min="0" max="90" step="1">
    </label>
    <br>
    <label for="max_cant">
        Maximo <span><?= $maxCant ? $maxCant : 100 ?></span>:
        <input type="range" name="max_cant" value="<?= $maxCant ? $maxCant : 100 ?>" min="10" max="100" step="1">
    </label>
    <br>
    <button type="submit">Enviar</button>

    <a href="<?= route_to('product.trace',$product->id) ?>">Limpiar</a>
</form>

<table>
    <thead>
        <tr>
            <th>
                ID
            </th>
            <th>
                Fecha
            </th>
            <th>
                Tipo
            </th>

            <th>
                Cantidad
            </th>

            <th>
                Usuario
            </th>
            <th>
                Descripción
            </th>
            <th>Dirección</th>
            <th>
                Precio
            </th>
            <th>
                Total
            </th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php foreach ($trace as $key => $t) : ?>
            <tr>
                <td>
                    <?= $t->id ?>
                </td>
                <td>
                    <?= $t->created_at ?>
                </td>
                <td>
                    <?= $t->type ?>
                </td>
                <td>
                    <?= $t->count ?>
                </td>

                <td>
                    <?= $t->email ?>
                </td>
                <td>
                    <?= $t->description ?>
                </td>
                <td>
                    <?= $t->direction ?>
                </td>
                <td>
                    <?= $product->price ?>
                </td>
                <td>
                    <?php $total += $product->price * $t->count ?>
                    <?= $product->price * $t->count ?>
                </td>
            </tr>
        <?php endforeach ?>
        <tr>

            <td colspan="8">Total</td>
            <td><?= $total ?></td>

        </tr>
    </tbody>
</table>

<script>
    formFilter = document.getElementById("formFilter")
    min_cant = document.querySelector("[name='min_cant']")
    max_cant = document.querySelector("[name='max_cant']")
    for_min_cant = document.querySelector("[for='min_cant']")
    for_max_cant = document.querySelector("[for='max_cant']")
    check_cant = document.querySelector("[name='check_cant']")

    check_cant.addEventListener('change', () => {
        if (check_cant.checked) {
            for_min_cant.style.display = "inline-block"
            for_max_cant.style.display = "inline-block"
        } else {
            for_min_cant.style.display = "none"
            for_max_cant.style.display = "none"
        }
    })

    min_cant.addEventListener('change', () => {
        for_min_cant.querySelector("span").innerText = min_cant.value
    })

    max_cant.addEventListener('change', () => {
        for_max_cant.querySelector("span").innerText = max_cant.value
    })

    formFilter.addEventListener('submit', (event) => {
        event.preventDefault()

        if (parseInt(min_cant.value) >= parseInt(max_cant.value))
            return alert("Seleccione un rango correcto por los dioses!")

        formFilter.submit()
    })
</script>