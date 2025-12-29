<?php
   namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $showingProductModal = false; // Contrôle l'affichage de la modale

    
    public $editingProductId = null;

    // Propriétés du formulaire
    public $name, $price, $weight, $unit, $stock, $image_url;

    protected $rules = [
        'name' => 'required|min:3',
        'price' => 'required|numeric',
        'weight' => 'required|numeric',
        'unit' => 'required',
        'stock' => 'required|integer',
        'image_url' => 'nullable|url',
    ];

    public function openModal()
    {
        $this->reset(['name', 'price', 'weight', 'unit', 'stock', 'image_url']);
        $this->showingProductModal = true;
    }

    // Charger les données pour la modification
    public function editProduct($id)
    {
        $this->resetErrorBag();
        $product = Product::findOrFail($id);
        
        $this->editingProductId = $id;
        $this->name = $product->name;
        $this->price = $product->price;
        $this->weight = $product->weight;
        $this->unit = $product->unit;
        $this->stock = $product->stock;
        $this->image_url = $product->image_url;

        $this->showingProductModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingProductId) {
            // Mise à jour
            $product = Product::find($this->editingProductId);
            $product->update([
                'name' => $this->name,
                'price' => $this->price,
                'weight' => $this->weight,
                'unit' => $this->unit,
                'stock' => $this->stock,
                'image_url' => $this->image_url,
            ]);
        } else {
            // Création
            Product::create([
                'name' => $this->name,
                'price' => $this->price,
                'weight' => $this->weight,
                'unit' => $this->unit,
                'stock' => $this->stock,
                'image_url' => $this->image_url,
            ]);
        }

        $this->showingProductModal = false;
    }

    public function deleteProduct($id)
    {
        Product::find($id)->delete();
    }

    public function render()
    {
        return view('livewire.products.index', [
            'products' => Product::where('name', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(5)
        ]);
    }
}