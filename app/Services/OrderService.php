<?php


namespace App\Services;


use App\Repositories\IOrderRepository;
use Illuminate\Http\Request;

class OrderService
{
    use ServiceHelpers;

    private IOrderRepository $repository;
    private CartService $cartService;

    public function __construct(IOrderRepository $repository,CartService $cartService)
    {
        $this->repository = $repository;
        $this->cartService = $cartService;
    }

    public function getAllOrders()
    {
        if (auth()->user()->is_admin)
            return $this->repository->getAllOrders();
        return $this->repository->getByUserId(auth()->id());
    }

    public function store(array $additionalData)
    {
        $products = $this->cartService->getItems()->map(function ($item){
            return [
                'id' => $item->id,
                'quantity' => $item->quantity
            ];
        })->all();
        $additionalData['user_id'] = auth()->id();
        $additionalData['total_money'] = $this->cartService->getTotalPrice();
        $order =  $this->repository->create($additionalData,$products);
        $this->cartService->clear();
        return $order;
    }

    public function delete($id)
    {
        abort_if(!auth()->user()->can("delete",$this->repository->findOrFail($id)),403 ,'Your limit to delete the order has ended or you are not authorized');
        $this->repository->delete($id);
    }

    public function update($id , array $data)
    {
        $this->idValidator($id);
        abort_if(!auth()->user()->can("update",$this->repository->findOrFail($id)),403 ,'Your limit to update the order has ended or you are not authorized');
        return $this->repository->update($id,$data);
    }

    public function show($id)
    {
        $this->idValidator($id);
        abort_if(!auth()->user()->can("view",$this->repository->findOrFail($id)),403 ,'Your limit to update the order has ended or you are not authorized');
        $order = $this->repository->findOrFail($id);
        $relations = ['products'];
        if (auth()->user()->is_admin)
            $relations[] = "user";
        $order->load($relations);
        return $order;
    }
}
