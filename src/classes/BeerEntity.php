<?php
class BeerEntity
{
    protected $beer_id;
    protected $name;
    protected $brewery;
    protected $importer;
	protected $ABV;
	protected $IBU;
	protected $style;
	protected $country;
	protected $grade;
	protected $image;
	protected $description;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['beer_id'])) {
            $this->beer_id = $data['beer_id'];
        }
        $this->name = $data['name'];	
	
		if (isset($data['brewery']))
        	$this->brewery = $data['brewery'];
		else
			$this->brewery = null;

		if (isset($data['importer']))
            $this->importer = $data['importer'];
        else
            $this->importer = null;
	
		if (isset($data['ABV']))
            $this->ABV = $data['ABV'];
        else
            $this->ABV = null;

		if (isset($data['IBU']))
            $this->IBU = $data['IBU'];
        else
            $this->IBU = null;

		if (isset($data['style']))
            $this->style = $data['style'];
        else
            $this->style = null;

		if (isset($data['country']))
            $this->country = $data['country'];
        else
            $this->country = null;

		if (isset($data['grade']))
            $this->grade = $data['grade'];
        else
            $this->grade = null;

		if (isset($data['image']))
            $this->image = $data['image'];
        else
            $this->image = null;

		if (isset($data['description']))
            $this->description = $data['description'];
        else
            $this->description = null;

    }
    public function getId() {
        return $this->beer_id;
    }
    public function getName() {
        return $this->name;
    }

	public function getEntities(){
		$entities = array("beer_id" => $this->beer_id, "name" => $this->name, "brewery" => $this->brewery, 
						"importer" => $this->importer, "ABV" => $this->ABV, "IBU" => $this->IBU, "style" => $this->style, 
						"country" => $this->country, "grade" => $this->grade, "image" => $this->image,
						"description" => $this->description);
		return $entities;	
	}
}
