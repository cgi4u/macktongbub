<?php
class BeerMapper extends Mapper
{
    public function getBeers() {
        $sql = "SELECT * from beer";
        $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new BeerEntity($row);
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
            return new BeerEntity($stmt->fetch());
        }
    }

    public function save(BeerEntity $beer) {
        $sql = "insert into beer
            (name, brewery, importer, ABV, IBU, style, country, grade, image, description) values
            (:name, :brewery, :importer, :ABV, :IBU, :style, :country, :grade, :image, :description )";
        $stmt = $this->db->prepare($sql);
		#echo $beer->getEntities();
		#echo json_decode($beer->getEntities())->{'brewery'};
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
    }
}
?>
