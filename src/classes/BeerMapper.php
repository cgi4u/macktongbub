<?php
class BeerMapper extends Mapper
{
    public function getBeers() {
        $sql = "SELECT * from beer";
        $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
			//echo $row['name'];
            $results[] = $row;
        }
        return $results;
    }
	
	public function getBeerByDetail($params){
		$sql = "SELECT * from beer where ";
		$andFlag = false;		

		if (isset($params['beer_id'])){
            $sql = $sql . "beer_id = " . $params['beer_id'] . " ";
            $andFlag = true;
        }
		if (isset($params['name'])){
			if ($andFlag) $sql = $sql . "and ";
			$sql = $sql . "name like '%" . $params['name'] . "%' ";
			$andFlag = true;
		}
		if (isset($params['country'])){
			if ($andFlag) $sql = $sql . "and ";
			$sql = $sql . "country = \"" . $params['country'] . "\" ";
			$andFlag = true;
		}
		if (isset($params['style'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "style = \"" . $params['style'] . "\" ";
            $andFlag = true;
        }
		if (isset($params['brewery'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "brewery like '%" . $params['brewery'] . "%' ";
            $andFlag = true;
        }
		if (isset($params['lABV'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "ABV >= " . $params['lABV'] . " ";
            $andFlag = true;
        }
		if (isset($params['hABV'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "ABV <= " . $params['hABV'] . " ";
            $andFlag = true;
        }
		if (isset($params['lIBU'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "IBU >= " . $params['lIBU'] . " ";
            $andFlag = true;
        }
        if (isset($params['hIBU'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "IBU <= " . $params['hIBU'] . " ";
            $andFlag = true;
        }
		if (isset($params['lgrade'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "grade >= " . $params['lgrade'] . " ";
            $andFlag = true;
        }

		$stmt = $this->db->query($sql);
		while($row = $stmt->fetch()) {
            //echo $row['name'];
            $results[] = $row;
        }
        return $results;
	}

    /**
     * Get one ticket by its ID
     *
     * @param int $ticket_id The ID of the ticket
     * @return TicketEntity  The ticket
     */
    public function getBeerById($beer_id) {
        $sql = "SELECT * from beer where beer_id = :beer_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["beer_id" => $beer_id]);
        if($result) {
            return $stmt->fetch();
        }
    }

    public function save(BeerEntity $beer) {	
        $sql = "insert into beer
            (name, brewery, importer, ABV, IBU, style, country, grade, image, description) values
            (:name, :brewery, :importer, :ABV, :IBU, :style, :country, :grade, :image, :description )";
        $stmt = $this->db->prepare($sql);
		$beer_entities = $beer->getEntities();
		
		$result = $stmt->execute([
            "name" => $beer_entities['name'],
            "brewery" => $beer_entities['brewery'],
            "importer" => $beer_entities['importer'],
			"ABV" => $beer_entities['ABV'],
            "IBU" => $beer_entities['IBU'],
            "style" => $beer_entities['style'],	
            "country" => $beer_entities['country'],
			"grade" => $beer_entities['grade'],
            "image" => $beer_entities['image'],
			"description" => $beer_entities['description']
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }
		
		$sql = "SELECT LAST_INSERT_ID()";
        $stmt = $this->db->query($sql);
		$save_result = $stmt->fetch();
		return $save_result;
    }

	public function modify($beer_id, BeerEntity $beer){
		$sql = "update beer set
            name = :name, brewery = :brewery, importer = :importer, ABV = :ABV, IBU = :IBU, 
			style = :style, country = :country, description = :description ";
		$beer_entities = $beer->getEntities();
		if (isset($beer_entities['image']))
			$sql = $sql . ", image = \"" . $beer_entities['image'] . "\" ";
		$sql = $sql . "where beer_id = :beer_id";
		$stmt = $this->db->prepare($sql);

		$result = $stmt->execute([
            "name" => $beer_entities['name'],
            "brewery" => $beer_entities['brewery'],
            "importer" => $beer_entities['importer'],
            "ABV" => $beer_entities['ABV'],
            "IBU" => $beer_entities['IBU'],
            "style" => $beer_entities['style'],
            "country" => $beer_entities['country'],
			"description" => $beer_entities['description'],
            "beer_id" => $beer_id
        ]);

		return $result;
	}
}
?>
