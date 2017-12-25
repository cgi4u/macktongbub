<?php
class BeerCommentsMapper extends Mapper
{
    public function getBeerCommentsById($beer_id) {
        $sql = "SELECT id, comment, grade from beer_comments where beer_id=" . $beer_id;
        $stmt = $this->db->query($sql);
        $results = [];
        while ($row = $stmt->fetch()) {
           	$results[] = $row;
        }
		return $results;
    }
		
	public function del($beer_id, $id, $password){
		$sql = "SELECT password from beer_comments where beer_id=" . $beer_id . " and id=" . $id;
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		$result = false;
		if ($row['password'] == $password){
			$sql = "delete from beer_comments where beer_id=" . $beer_id . " and id=" . $id;
			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute();
			if (!$result)
				return $result;
			
			$sql = "select avg(grade) from beer_comments where beer_id=".$beer_id;
        	$stmt = $this->db->query($sql);
        	$row = $stmt->fetch();
        	$grade = $row['avg(grade)'];
			if ($grade == null){
				$sql = "update beer set grade=null where beer_id=".$beer_id;
			}
			else{
        		$sql = "update beer set grade=".$grade." where beer_id=".$beer_id;
			}
        	$stmt = $this->db->prepare($sql);
        	$result = $stmt->execute();
		}
		return $result;
	}

    public function save($beer_comment) {	
		$sql = "select max(id) from beer_comments where beer_id=". $beer_comment['beer_id'];
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		if ($row['max(id)'] != null)
			$id = $row['max(id)'] + 1;
		else
			$id	= 0;

        $sql = "insert into beer_comments
            (beer_id, id, comment, grade, password) values
            (:beer_id, :id, :comment, :grade, :password)";
        $stmt = $this->db->prepare($sql);
	
		//return $beer_comment['beer_id']." ".$id." ".$beer_comment['comment']." ".$beer_comment['grade']." ".$beer_comment['password'];
	
		$result = $stmt->execute([
            "beer_id" => $beer_comment['beer_id'],
			"id" => $id,
            "comment" => $beer_comment['comment'],
            "grade" => $beer_comment['grade'],
			"password" => $beer_comment['password']
        ]);

        if(!$result) {
            throw new Exception("could not save record");
        }

		$sql = "select avg(grade) from beer_comments where beer_id=".$beer_comment['beer_id'];
		$stmt = $this->db->query($sql);
		$row = $stmt->fetch();
		$grade = $row['avg(grade)'];
		$sql = "update beer set grade=".$grade." where beer_id=".$beer_comment['beer_id'];
		$stmt = $this->db->prepare($sql);
		$result = $stmt->execute();
		
		return $result;
    }
}
?>
