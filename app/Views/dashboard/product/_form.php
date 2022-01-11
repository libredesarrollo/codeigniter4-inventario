<script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script>

<label for="name">Nombre</label>
<input type="text" id="name" name="name" value="<?= old('name', $product->name) ?>" />

<label for="code">Código</label>
<input type="text" id="code" name="code" value="<?= old('code', $product->code) ?>" />

<label for="description">Descripción</label>
<textarea id="editor" name="description"><?= old('description', $product->description) ?></textarea>

<label for="entry">Entrada</label>
<input type="number" id="entry" name="entry" value="<?= old('entry', $product->entry) ?>" />

<label for="exit">Salida</label>
<input type="text" id="exit" name="exit" value="<?= old('exit', $product->exit) ?>" />

<label for="stock">Stock</label>
<input type="text" id="stock" name="stock" value="<?= old('stock', $product->stock) ?>" />

<label for="price">Precio</label>
<input type="text" id="price" name="price" value="<?= old('price', $product->price) ?>" />

<label for="category_id">Categorías</label>
<select name="category_id" id="category_id">

    <?php foreach ($categories as $c) : ?>
        <option <?= $product->category_id == $c->id ? "selected" : "" ?> value="<?= $c->id ?>"><?= $c->name ?></option>
    <?php endforeach ?>

</select>

<label for="tag_id">Tags</label>
<select multiple name="tag_id[]" id="tag_id">
    <?php foreach ($tags as $t) : ?>
        <option
        <?= in_array($t->id, old('tag_id',$productTags)) ? "selected" : "" ?>
        value="<?= $t->id ?>"><?= $t->name ?></option>
    <?php endforeach ?>
</select>

<button type="submit"><?= $textButton ?></button>


<script>
    ClassicEditor.create(document.querySelector("#editor"))
</script>