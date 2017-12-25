<?php
class SellerForBeerMapper extends Mapper
{

    public function save($seller_id, $beer_id) {	
        $sql = "insert into seller_for_beer
            (seller_id, beer_id) values
            (:seller_id, :beer_id)";
        $stmt = $this->db->prepare($sql);
		
		$result = $stmt->execute([
            "seller_id" => $seller_id,
            "beer_id" => $beer_id
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }
		
		return $result;
    }

	public function getBeerBySeller($seller_id){
		$sql = 	"select * from beer  
					join seller_for_beer  
					on beer.beer_id = seller_for_beer.beer_id 
					where seller_for_beer.seller_id=".$seller_id;

		$stmt = $this->db->query($sql);
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;		
	}

	public function getSellerByBeer($beer_id){
        $sql =  "select * from seller
                    join seller_for_beer
                    on seller.seller_id = seller_for_beer.seller_id
                    where seller_for_beer.beer_id=".$beer_id;

        $stmt = $this->db->query($sql);
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;
    }
}
?>
