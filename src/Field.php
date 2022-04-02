<?php namespace App;

class Field {

    private $name;
    private $type;
    private $required;
    private $label;
    private $value;
    private $helpText;
    private $placeholder;

    public function __construct(
        string $name, 
        string $type = 'text',  
        bool $required = false,
        ?string $label = null, 
        ?string $value = null,
        ?string $helpText = null,
        string $placeholder = '***'
        ) 
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->label = $label;
        $this->value = $value;
        $this->helpText = $helpText;
        $this->placeholder = $placeholder;
        $this->setLabel($label);
    }
    
    public function html(): string
    { 
        $required = $this->required ? ' required' : ''; 
        $html = '';
        if($this->type === 'text' || $this->type === 'email') {
            $value = !empty($this->value) ? ' value="'.$this->value.'" ' : ' ';
            $html .= '<input type="'.$this->type.'" name="'.$this->name.'"'.$value.'class="form-control" placeholder="'.$this->placeholder.'"'.$required.'">';
            $html .= '<label>'.$this->label.'</label>';
        } elseif($this->type === 'textarea') {
            $value = !empty($this->value) ? $this->value : '';
            $html .= '<textarea name="'.$this->name.'" class="form-control" rows="4" cols="50" placeholder="'.$this->label.'"'.$required.'>'.$value.'</textarea>';
        } else {
            throw new \Exception("Unvalid parameter entered in Field::html(). Allowed are: 'text', 'email' and 'textarea' for now.");
        }
        if($this->helpText) {
            $html .= '<div class="form-text">'.$this->helpText.'</div>';
        }
        ob_start();
        if($this->type === 'textarea') {
            echo '<div class="mb-3">'.$html.'</div>';
        } else {
            echo '<div class="form-floating mb-3">'.$html.'</div>';
        }
        return ob_get_clean();
    }

    private function setLabel(?string $label): void 
    {
        if($this->label === null) {
            $this->label = ucFirst($this->name);
        } else {
            $this->label = $label;
        }
    }

}