
<?php
require_once 'models/Food.php';
require_once 'models/Category.php';

class FoodController {

    public function menu() {
        $category_id = $_GET['category_id'] ?? null;
        $foodModel = new Food();
        
        if($category_id) {
            $foods = $foodModel->getByCategory($category_id);
        } else {
            $foods = $foodModel->all();
        }
        
        $categoryModel = new Category();
        $categories = $categoryModel->all();
        
        require 'views/foods/menu.php';
    }
}
