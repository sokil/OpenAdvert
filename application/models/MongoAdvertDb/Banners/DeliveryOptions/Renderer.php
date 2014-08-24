<?php

namespace MongoAdvertDb\Banners\DeliveryOptions;

class Renderer {
    
    public function render(Option $option, $index)
    {
        $templateFile = __DIR__ . '/Option/' . $option->getTemplate() . '.php';
        if(!file_exists($templateFile)) {
            throw new \Exception('Template not found');
        }
        
        echo '<div class="deliveryOption" id="deliveryOption' . $index . '">';
        require $templateFile;
        echo '</div>';
    }
}
