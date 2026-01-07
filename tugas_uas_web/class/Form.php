<?php

class Form {
    private $action;
    private $fields = [];

    public function __construct($action) {
        $this->action = $action;
    }

    public function addField($name, $label, $type = "text") {
        $this->fields[] = [
            'type' => $type,
            'name' => $name,
            'label' => $label
        ];
    }

    public function addTextarea($name, $label) {
        $this->fields[] = [
            'type' => 'textarea',
            'name' => $name,
            'label' => $label
        ];
    }

    public function display() {
        echo "<form method='POST' action='{$this->action}' class='simple-form'>";
        
        foreach ($this->fields as $f) {
            echo "<label><b>{$f['label']}</b></label><br>";
            
            if ($f['type'] === 'textarea') {
                echo "<textarea name='{$f['name']}' class='form-textarea'></textarea><br>";
            } else {
                echo "<input type='{$f['type']}' name='{$f['name']}' class='form-input'><br>";
            }
        }
        
        echo "<button type='submit' class='btn btn-primary'>Simpan</button>";
        echo " <a href='?url=artikel/index' class='btn btn-outline'>Batal</a>";
        echo "</form>";
    }
}
?>