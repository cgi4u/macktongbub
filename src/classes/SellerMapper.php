<?php
class SellerMapper extends Mapper
{
    public function getSellers() {
        $sql = "SELECT seller_id, name, address, phone, type, grade, image, description, short_addr, lat, lng from seller";
        $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }
        return $results;
    }
	
	public function getSellerByDetail($params){
		$sql = "SELECT seller_id, name, address, phone, type, grade, image, description, short_addr, lat, lng from seller where ";
		$andFlag = false;		

		if (isset($params['seller_id'])){
            $sql = $sql . "seller_id = " . $params['seller_id'] . " ";
            $andFlag = true;
        }
		if (isset($params['name'])){
			if ($andFlag) $sql = $sql . "and ";
			$sql = $sql . "name like '%" . $params['name'] . "%' ";
			$andFlag = true;
		}
		if (isset($params['type'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "type = " . $params['type'] . " ";
            $andFlag = true;
        }
		if (isset($params['address'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "address like '%" . $params['address'] . "%' ";
            $andFlag = true;
        }
		if (isset($params['lgrade'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "grade >= " . $params['lgrade'] . " ";
            $andFlag = true;
        }
		if (isset($params['short_addr'])){
            if ($andFlag) $sql = $sql . "and ";
            $sql = $sql . "short_addr = ".$params['short_addr']." ";
            $andFlag = true;
        }

		$stmt = $this->db->query($sql);
		while($row = $stmt->fetch()) {
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
	/*
    public function getSellerById($beer_id) {
        $sql = "SELECT seller_id, name, address, phone, type, grade, image, description, short_addr, lat, lng from seller where beer_id = :beer_id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(["beer_id" => $beer_id]);
        if($result) {
            return $stmt->fetch();
        }
    }*/

    public function save($new_seller) {	
        $sql = "insert into seller
            (name, address, phone, type, grade, image, description, short_addr, lat, lng) values
            (:name, :address, :phone, :type, :grade, :image, :description, :short_addr, :lat, :lng)";
        $stmt = $this->db->prepare($sql);
		
		$result = $stmt->execute([
            "name" => $new_seller['name'],
            "address" => $new_seller['address'],
            "phone" => $new_seller['phone'],
			"type" => $new_seller['type'],
            "grade" => $new_seller['grade'],
            "image" => $new_seller['image'],
			"short_addr" => $new_seller['short_addr'],	
            "lat" => $new_seller['lat'],
			"lng" => $new_seller['lng'],
			"description" => $new_seller['description']
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }
		
		return $result;
    }

	public function modify($seller_id, $seller){
		$sql = "update seller set
            name = :name, address = :address, short_addr = :short_addr, lat = :lat, lng = :lng, 
			type = :type, description = :description, phone = :phone ";
		if (isset($seller['image']))
			$sql = $sql . ", image = \"" . $seller['image'] . "\" ";
		$sql = $sql . "where seller_id = :seller_id";
		$stmt = $this->db->prepare($sql);
		
		#return $seller;

		$result = $stmt->execute([
            "name" => $seller['name'],
            "address" => $seller['address'],
            "short_addr" => $seller['short_addr'],
            "lat" => $seller['lat'],
            "lng" => $seller['lng'],
            "type" => $seller['type'],
			"description" => $seller['description'],
			"phone" => $seller['phone'],
            "seller_id" => $seller_id
        ]);

		return $result;
	}
}
?>
