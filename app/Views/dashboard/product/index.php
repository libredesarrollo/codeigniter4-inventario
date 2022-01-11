<a href="/dashboard/product/new"> Crear</a>


<div id="blockSelectUser" style="display: none;">
    <select class="user">
        <?php foreach ($users as $key => $u) : ?>
            <option value="<?= $u->id ?>"><?= $u->username ?></option>
        <?php endforeach ?>
    </select>

    <label class="errorDescription"></label>
    <textarea class="description" placeholder="Desripción"></textarea>
    <label class="errorDirection"></label>
    <textarea class="direction" placeholder="Direción"></textarea>

    <button class="user">
        Enviar
    </button>
</div>

<form>
    <label for="category_id">Categorías</label>
    <select name="category_id" id="category_id">
        <option value=""></option>
        <?php foreach ($categories as $c) : ?>
            <option <?= $category_id == $c->id ? "selected" : "" ?> value="<?= $c->id ?>"><?= $c->name ?></option>
        <?php endforeach ?>
    </select>

    <!-- <label for="tags_id">Tags</label> -->

    <!-- <select multiple name="tags_id[]" id="tags_id">
    <option value=""></option>
        <?php foreach ($tags as $t) : ?>
            <option <?= in_array($t->id, old('tag_id', $productTags)) ? "selected" : "" ?> value="<?= $t->id ?>"><?= $t->name ?></option>
        <?php endforeach ?>
    </select> -->

    <br>
    <?php foreach ($tags as $t) : ?>
        <label for="t_<?= $t->id ?>"><?= $t->name ?></label>
        <input value="<?= $t->id ?>" <?= in_array($t->id, old('tag_id', $productTags)) ? "checked" : "" ?> type="checkbox" name="tags_id[]" id="t_<?= $t->id ?>">
        <br>
    <?php endforeach ?>


    <button type="submit">Enviar</button>
</form>

<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Nombre</th>
            <th>Código</th>
            <th>Entradas</th>
            <th>Salidas</th>
            <th>Stock</th>
            <th>Precio</th>
            <th>Opciones</th>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($products as $key => $p) : ?>
            <tr>
                <td><?= $p->id ?></td>
                <td><?= $p->name ?></td>
                <td><?= $p->code ?></td>
                <td><input type="number" data-id="<?= $p->id ?>" class="entry" value="<?= $p->entry ?>" /></td>
                <td>
                <td><input type="number" data-id="<?= $p->id ?>" class="exit" value="<?= $p->exit ?>" /></td>
                </td>
                <td id="stock_<?= $p->id ?>"><?= $p->stock ?></td>
                <td><?= $p->price ?></td>
                <td>

                    <form action="/dashboard/product/delete/<?= $p->id ?>" method="POST">
                        <button>Eliminar</button>
                    </form>

                    <a href="/dashboard/product/<?= $p->id ?>/edit">Editar</a>
                    <a href="<?= route_to('product.trace', $p->id) ?>">Traza</a>

                </td>
            </tr>
        <?php endforeach ?>



    </tbody>
</table>

<?= $pager->links() ?>


<script>
    entries = document.querySelectorAll(".entry")

    exits = document.querySelectorAll(".exit")
    blockSelectUser = document.querySelector("#blockSelectUser")
    selectUser = document.querySelector("#blockSelectUser select.user")
    buttonUser = document.querySelector("#blockSelectUser button.user")
    direction = document.querySelector("#blockSelectUser .direction")
    description = document.querySelector("#blockSelectUser .description")
    //labels
    errorDescription = document.querySelector("#blockSelectUser .errorDescription")
    errorDirection = document.querySelector("#blockSelectUser .errorDirection")

    typeUser = "customer"
    userExit = []
    userEntry = []


    function getUsers() {
        fetch("/dashboard/user/get-by-type/" + typeUser)
            .then((res) => res.json())
            .then((res) => {
                if (typeUser == "provider")
                    userEntry = res
                else
                    userExit = res

                populateSelectUser()
            })
    }

    function populateSelectUser() {
        selectUser.options.length = 0

        dataArray = typeUser == "customer" ? userExit : userEntry

        for (index in dataArray) {
            selectUser.options[selectUser.options.length] = new Option(dataArray[index].username + " " + dataArray[index].type, dataArray[index].id)
        }
    }

    entries.forEach(function(entry) {
        entry.addEventListener('keyup', function(event) {

            id = entry.getAttribute('data-id')
            buttonUser.setAttribute('data-id', id)
            buttonUser.setAttribute('data-value', entry.value)
            buttonUser.setAttribute('data-type', 'entry')

            typeUser = "provider"

            if (event.keyCode === 13) {
                blockSelectUser.style.display = "block"
            }

            if (userEntry.length == 0)
                getUsers()
            else
                populateSelectUser()
        });
    })




    exits.forEach(function(exit) {

        exit.addEventListener('keyup', function(event) {
            id = exit.getAttribute('data-id')
            buttonUser.setAttribute('data-id', id)
            buttonUser.setAttribute('data-value', exit.value)
            buttonUser.setAttribute('data-type', 'exit')

            typeUser = "customer"

            if (event.keyCode === 13) {
                blockSelectUser.style.display = "block"
            }

            if (userExit.length == 0)
                getUsers()
            else
                populateSelectUser()

        });
    })

    buttonUser.addEventListener("click", function() {



        id = buttonUser.getAttribute('data-id')
        value = buttonUser.getAttribute('data-value')
        type = buttonUser.getAttribute('data-type')
        userId = selectUser.value

        url = `/dashboard/product/add-stock/${id}/${value}`
        if (type == "exit")
            url = `/dashboard/product/exit-stock/${id}/${value}`

        var formData = new FormData()
        formData.append('user_id', userId)
        formData.append('direction', direction.value)
        formData.append('description', description.value)

        fetch(url, {
                method: 'POST',
                body: formData
            }).then((res) => {
                return res.json()
            })
            .then((res) => {
                //problemas con la respuesta
                switch (res.status) {
                    case 400:
                        console.log(res.messages)
                        console.log(res.messages['direction'])

                        errorDirection.innerText = res.messages['direction']
                        errorDescription.innerText = res.messages['description']

                        throw new Error("Errores de validacion")
                        break
                }

                // 200 ok

                blockSelectUser.style.display = "none"
                resetForm()

                document.getElementById("stock_" + res.id).innerText = res.stock
            })
            .catch((res) => {
                console.log(res)
            })
    })

    function resetForm() {
        errorDirection.innerText = ""
        errorDescription.innerText = ""
        direction.value = ""
        description.value = ""
    }
</script>

<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>