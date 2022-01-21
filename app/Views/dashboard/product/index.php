<a href="/dashboard/product/new" class="btn btn-primary"> Crear</a>

<div class="modal" tabindex="-1" id="blockSelectUser">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestión ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <select class="form-control user">
                    <?php foreach ($users as $key => $u) : ?>
                        <option value="<?= $u->id ?>"><?= $u->username ?></option>
                    <?php endforeach ?>
                </select>

                <label class="errorDescription"></label>
                <textarea class="form-control description" placeholder="Desripción"></textarea>
                <label class="errorDirection"></label>
                <textarea class="form-control direction" placeholder="Direción"></textarea>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button class="user btn btn-success">
                    Enviar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card mt-2">
    <div class="card-header">
        <button data-bs-toggle="collapse" data-bs-target="#filters" class="btn btn-sm btn-flat float-end">Ver</button>
        <h4 class="mb-0">Filtros</h4>
    </div>
    <div class="card-body collapse" id="filters">
        <form>
            <label for="category_id">Categorías</label>
            <select class="form-control" name="category_id" id="category_id">
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

            <div class="overflow-auto mt-2" style="max-height:132px">
                <ul class="list-group">
                    <?php foreach ($tags as $t) : ?>
                        <li class="list-group-item">
                            <label for="t_<?= $t->id ?>"><?= $t->name ?></label>
                            <input value="<?= $t->id ?>" <?= in_array($t->id, old('tag_id', $productTags)) ? "checked" : "" ?> type="checkbox" name="tags_id[]" id="t_<?= $t->id ?>">
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>

            <button type="submit" class="mt-3 user btn btn-success">Enviar</button>
        </form>
    </div>
</div>

<table class="table mt-3">
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
                <td><input type="number" data-id="<?= $p->id ?>" class="entry form-control" value="<?= $p->entry ?>" /></td>
                <td><input type="number" data-id="<?= $p->id ?>" class="exit form-control" value="<?= $p->exit ?>" /></td>
                </td>
                <td id="stock_<?= $p->id ?>"><?= $p->stock ?></td>
                <td><?= $p->price ?></td>
                <td>

                    <form action="/dashboard/product/delete/<?= $p->id ?>" method="POST">
                        <button class="btn btn-danger btn-sm">Eliminar</button>
                    </form>

                    <a class="btn btn-sm btn-flat d-block" href="/dashboard/product/<?= $p->id ?>/edit">Editar</a>
                    <a class="btn btn-sm btn-flat  d-block" href="<?= route_to('product.trace', $p->id) ?>">Traza</a>

                </td>
            </tr>
        <?php endforeach ?>



    </tbody>
</table>

<?= $pager->links() ?>


<script>
    modal = new bootstrap.Modal(document.getElementById('blockSelectUser'))

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

            modal.show()

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

            modal.show()

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

            modal.hide()

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