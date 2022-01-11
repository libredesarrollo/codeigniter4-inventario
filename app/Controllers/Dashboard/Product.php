<?php

namespace App\Controllers\Dashboard;

use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\ProductControlModel;
use App\Models\ProductUserControlModel;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductTagModel;
use App\Models\TagModel;
use CodeIgniter\API\ResponseTrait;
use \CodeIgniter\Exceptions\PageNotFoundException;

use Dompdf\Dompdf;

class Product extends BaseController
{

    use ResponseTrait;

    public function demoPDF()
    {
        $productModel = new ProductModel();
        $productId = 1;

        $product = $productModel->asObject()->find($productId);

        $query = $productModel->asObject()->select("pc.*, u.email, puc.description, puc.direction")
            ->join('products_control as pc', 'pc.product_id = products.id')
            ->join('users as u', 'pc.user_id = u.id')
            ->join('products_users_control as puc', 'pc.id = puc.product_control_id');

        $data = [
            'product' => $product,
            'trace' => $query->where('products.id', $productId)
                ->findAll()
        ];

        $dompdf = new Dompdf();
        //$dompdf->loadHTML('<h1>Hola Mundo</h1><br><p>Otro contenido</p>');
        $dompdf->loadHTML(view("dashboard/product/trace_pdf", $data));

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $dompdf->stream();
    }

    public function trace($productId)
    {
        $productModel = new ProductModel();
        $userModel = new UserModel();

        $product = $productModel->asObject()->find($productId);



        if ($product == null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $res = $this->validate([
            'min_cant' => 'is_natural',
            'max_cant' => 'is_natural',
        ]);

        if (!$res)
            echo "Error";

        $query = $productModel->asObject()->select("pc.*, u.email, puc.description, puc.direction")
            ->join('products_control as pc', 'pc.product_id = products.id')
            ->join('users as u', 'pc.user_id = u.id')
            ->join('products_users_control as puc', 'pc.id = puc.product_control_id');

        $type = $this->request->getGet('type');
        if ($type !== "" &&  ($type == "entry" || $type == "exit"))
            $query->where('pc.type', $type);
        else
            $type = "";


        // usuarios
        if ($type == "exit") {
            $users = $userModel->asObject()
                ->where("type", "customer")
                ->findAll();
        } else if ($type == "entry") {
            $users = $userModel->asObject()
                ->where("type", "provider")
                ->findAll();
        } else {
            $users = $userModel->asObject()->findAll();
        }

        $userId = intval($this->request->getGet('user_id'));

        if ($userId !== "" && $userId > 0) {
            // $user = $userModel->asObject()->find($userId);
            // if ($user !== null)
            $query->where('pc.user_id', $userId);
        }

        if ($this->request->getGet('check_cant')) {
            $query->where('pc.count >=', $this->request->getGet('min_cant'));
            $query->where('pc.count <=', $this->request->getGet('max_cant'));
        }

        $searchs = explode(" ", trim($this->request->getGet('search')));

        if ($this->request->getGet('search')) {
            //->orGroupStart()
            $query->groupStart();
            foreach ($searchs as $s) {
                $query->orLike('u.username', $s)
                    ->orLike('u.email', $s)
                    ->orLike('puc.description', $s);
            }
            $query->groupEnd();
        }

        //echo $query->where('products.id', $productId)->getCompiledSelect();

        $data = [
            'product' => $product,
            'users' => $users,
            'userId' => $userId,
            'typeId' => $type,
            'search' => $this->request->getGet('search'),
            'checkCant' => $this->request->getGet('check_cant'),
            'minCant' => $this->request->getGet('min_cant'),
            'maxCant' => $this->request->getGet('max_cant'),
            'trace' => $query->where('products.id', $productId)
                ->findAll()
        ];

        $this->_loadDefaultView(
            'Traza del producto ' . $product->name,
            $data,
            'trace'
        );
    }

    public function addStock($id, $entry)
    {

        // validar

        $validation = \Config\Services::validation();

        if (!$validation->check($entry, 'required|is_natural_no_zero')) {
            return $this->failValidationErrors("Cantidad no es valida");
        }

        //aumentar el stock
        $productModel = new ProductModel();
        $productControlModel = new ProductControlModel();
        $productUserControlModel = new ProductUserControlModel();

        $userId = $this->request->getPost('user_id');

        $res = $this->validate([
            'user_id' => 'required|checkType[provider]',
            'direction' => 'required|min_length[2]',
            'description' => 'required|min_length[2]',
        ]);

        // busqueda BD

        $product = $productModel->asObject()->find($id);

        // validaciones

        if (!$res) {
            return  $this->failValidationErrors([
                "description" => $this->validator->getError("direction"),
                "direction" => $this->validator->getError("description")
            ]);
        }

        if ($product == null)
            throw PageNotFoundException::forPageNotFound();

        $product->stock += $entry;
        $product->entry = $entry;

        $productModel->update($id, [
            'entry' => $product->entry,
            'stock' => $product->stock
        ]);

        $productControlId = $productControlModel->insert([
            'product_id' => $id,
            'count' => $entry,
            'type' => 'entry',
            'user_id' => $userId
        ]);

        $productUserControlModel->insert(
            [
                'product_control_id' => $productControlId,
                'direction' => $this->request->getPost('direction'),
                'description' => $this->request->getPost('description'),
                'user_id' => $userId
            ]
        );




        // $productControlModel->update(1, [
        //     'count' => 50,
        // ]);

        return $this->respondUpdated($product);
        // return $this->respondDeleted(["stock" => 50]);
        //return $this->failForbidden();
    }
    public function exitStock($id, $exit)
    {
        // validaciones
        $validation = \Config\Services::validation();

        if (!$validation->check($exit, 'required|is_natural_no_zero')) {
            return $this->failValidationErrors("Cantidad no es valida");
        }

        $productModel = new ProductModel();
        $productControlModel = new ProductControlModel();
        $productUserControlModel = new ProductUserControlModel();

        $res = $this->validate([
            'user_id' => 'required|customer',
            'direction' => 'required|min_length[2]',
            'description' => 'required|min_length[2]',
        ]);

        if (!$res) {
            return $this->failValidationErrors([
                "description" => $this->validator->getError("direction"),
                "direction" => $this->validator->getError("description")
            ]);
        }

        $userId = $this->request->getPost('user_id');
        $product = $productModel->asObject()->find($id);

        if ($product == null)
            throw PageNotFoundException::forPageNotFound();

        if ($product->stock - $exit < 0) {
            return $this->failValidationErrors("No hay stock suficiente", 400);
        }

        $product->stock -= $exit;
        $product->exit = $exit;

        $productModel->update($id, [
            'exit' => $product->exit,
            'stock' => $product->stock
        ]);

        $productControlId = $productControlModel->insert([
            'product_id' => $id,
            'count' => $exit,
            'type' => 'exit',
            'user_id' => $userId
        ]);

        $productUserControlModel->insert(
            [
                'product_control_id' => $productControlId,
                'direction' => $this->request->getPost('direction'),
                'description' => $this->request->getPost('description'),
                'user_id' => $userId
            ]
        );

        return $this->respondUpdated($product);
    }

    public function index()
    {

        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $userModel = new UserModel();
        $tagModel = new TagModel();
        $productTagModel = new ProductTagModel();

        $query = $productModel->asObject()->select('id,code,exit,entry,stock,price,name');

        $category_id = $this->request->getGet('category_id');

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        $tags_id = $this->request->getGet('tags_id') ?: [];

        if ($tags_id) {

            // $productsId = [];//array_column($productTagModel->select('product_id')->whereIn('tag_id',$tags_id)->findAll(), 'product_id');

            // if(count($productsId) > 0)
            // $query->whereIn('id',$productsId);

            $query->join('product_tag as pt', 'pt.product_id = products.id')
            ->groupBy('id,code,exit,entry,stock,price,name');
           
        }

        $data = [
            'categories' => $categoryModel->asObject()->findAll(),
            'tags' => $tagModel->asObject()->findAll(),
            'category_id' => $category_id,
            'productTags' => $tags_id,
            'products' => $query->paginate(10),
            'users' => $userModel->asObject()->where('type', 'customer')->findAll(),
            'pager' => $productModel->pager
        ];

        $this->_loadDefaultView('Listado de productos', $data, 'index');
    }

    public function new()
    {

        $categoryModel = new CategoryModel();
        $tagModel = new TagModel();
        $productTagModel = new ProductTagModel();

        //mkdir('writeable/uploads/test',0755,true);

        $validation =  \Config\Services::validation();
        $this->_loadDefaultView('Crear etiqueta', ['validation' => $validation, 'product' => new ProductModel(), 'categories' => $categoryModel->asObject()->findAll(), 'tags' => $tagModel->asObject()->findAll(), 'productTags' => []], 'new');
    }

    public function create()
    {

        $product = new ProductModel();
        $productTagModel = new ProductTagModel();

        if ($this->validate('products')) {
            $id = $product->insert([
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'code' => $this->request->getPost('code'),
                'entry' => $this->request->getPost('entry'),
                'exit' => $this->request->getPost('exit'),
                'stock' => $this->request->getPost('stock'),
                'category_id' => $this->request->getPost('category_id'),
                'price' => $this->request->getPost('price'),
            ]);

            $tags = $this->request->getPost('tag_id') ?: [];

            foreach ($tags as $t) {
                $productTagModel->insert(
                    [
                        'product_id' => $id,
                        'tag_id' => $t
                    ]
                );
            }

            return redirect()->to("/dashboard/product/$id/edit")->with('message', 'Película creada con éxito.');
        } else {
            session()->setFlashdata([
                'validation' => $this->validator
            ]);
        }

        return redirect()->back()->withInput();
    }

    public function edit($id = null)
    {

        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $tagModel = new TagModel();
        $productTagModel = new ProductTagModel();

        $productTags = array_column($productTagModel
            ->asArray()
            ->select("tag_id")
            ->where('product_id', $id)
            ->findAll(), "tag_id");

        if ($productModel->find($id) == null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $validation =  \Config\Services::validation();
        $this->_loadDefaultView(
            'Actualizar etiqueta',
            [
                'validation' => $validation,
                'product' => $productModel->asObject()->find($id),
                'categories' => $categoryModel->asObject()->findAll(),
                'tags' => $tagModel->asObject()->findAll(),
                'productTags' => $productTags

            ],
            'edit'
        );
    }

    public function update($id = null)
    {

        $product = new ProductModel();
        $productTagModel = new ProductTagModel();

        if ($product->find($id) == null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ($this->validate('products')) {

            $tags = $this->request->getPost('tag_id') ?: [];

            $product->update($id, [
                'name' => $this->request->getPost('name'),
                'description' => $this->request->getPost('description'),
                'code' => $this->request->getPost('code'),
                'entry' => $this->request->getPost('entry'),
                'exit' => $this->request->getPost('exit'),
                'stock' => $this->request->getPost('stock'),
                'category_id' => $this->request->getPost('category_id'),
                'price' => $this->request->getPost('price'),
            ]);

            // tags

            // eliminacion de las etiquetas

            $productTagModel
                ->whereNotIn('tag_id', $tags)
                ->where('product_id', $id)->delete();

            foreach ($tags as $t) {
                try {
                    $productTagModel->insert(
                        [
                            'product_id' => $id,
                            'tag_id' => $t
                        ]
                    );
                } catch (\Throwable $th) {
                }
            }



            return redirect()->to('/dashboard/product')->with('message', 'Película editada con éxito.');
        } else {
            session()->setFlashdata([
                'validation' => $this->validator
            ]);
        }

        return redirect()->back()->withInput();
    }

    public function delete($id = null)
    {

        $product = new ProductModel();

        if ($product->find($id) == null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $product->delete($id);

        return redirect()->to('/dashboard/product')->with('message', 'cateogría eliminada con éxito.');
    }

    private function _loadDefaultView($title, $data, $view)
    {

        $dataHeader = [
            'title' => $title
        ];

        echo view("dashboard/templates/header", $dataHeader);
        echo view("dashboard/product/$view", $data);
        echo view("dashboard/templates/footer");
    }
}
