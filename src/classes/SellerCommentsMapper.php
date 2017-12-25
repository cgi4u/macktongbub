<?php
class SellerCommentsMapper extends Mapper
{
    public function getSellerCommentsById($seller_id) {
        $sql = "SELECT id, comment, grade from seller_comments where seller_id=" . $seller_id;
        $stmt = $this->db->query($sql);
        $results = [];
        while ($row = $stmt->fetch()) {
           	$results[] = $row;
        }
		return $results;
    }
		
	public function del($seller_id, $id, $password){
		$sql = "SELECT password from seller_comments where seller_id=" . $seller_id . " and id=" . $id;
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		$result = false;
		if ($row['password'] == $password){
			$sql = "delete from seller_comments where seller_id=" . $seller_id . " and id=" . $id;
			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute();
			if (!$result)
				return $result;
			
			$sql = "select avg(grade) from seller_comments where seller_id=".$seller_id;
        	$stmt = $this->db->query($sql);
        	$row = $stmt->fetch();
        	$grade = $row['avg(grade)'];
			if ($grade == null){
				$sql = "update seller set grade=null where seller_id=".$seller_id;
			}
			else{
        		$sql = "update seller set grade=".$grade." where seller_id=".$seller_id;
			}
        	$stmt = $this->db->prepare($sql);
        	$result = $stmt->execute();
		}
		return $result;
	}

    public function save($seller_comment) {	
		$sql = "select max(id) from seller_comments where seller_id=". $seller_comment['seller_id'];
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		if ($row['max(id)'] != null)
			$id = $row['max(id)'] + 1;
		else
			$id	= 0;

        $sql = "insert into seller_comments
            (seller_id, id, comment, grade, password) values
            (:seller_id, :id, :comment, :grade, :password)";
        $stmt = $this->db->prepare($sql);
	
		//return $seller_comment['seller_id']." ".$id." ".$seller_comment['comment']." ".$seller_comment['grade']." ".$seller_comment['password'];
	
		$result = $stmt->execute([
            "seller_id" => $seller_comment['seller_id'],
			"id" => $id,
            "comment" => $seller_comment['comment'],
            "grade" => $seller_comment['grade'],
			"password" => $seller_comment['password']
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }

		$sql = "select avg(grade) from seller_comments where seller_id=".$seller_comment['seller_id'];
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		$grade = $row['avg(grade)'];
		$sql = "update seller set grade=".$grade." where seller_id=".$seller_comment['seller_id'];
		$stmt = $this->db->prepare($sql);
		$result = $stmt->execute();
		
		return $result;
    }
}
?>
