<?php 
// forked pagination class from Profile Builder.

class wck_stp_pagination{
	var $pag = 1; // Current Page
	var $perPage = 10; // Items on each pag, defaulted to 10
	var $showFirstAndLast = true; // if you would like the first and last pag options.

	function generate($total, $perPage = 10, $searchFor, $first, $prev, $next, $last, $currentPage){
		//Assign the pag navigation buttons
		$this->first = $first;
		$this->prev = $prev;
		$this->next = $next;
		$this->last = $last;
		$this->implodeBy = NULL; //this variable wasn't set, so it was NULL either way
		
		//Current Page
		$this->currentPage = (int)$currentPage;
	
		//Assign search variable
		if ($searchFor != '')
			$this->searchFor = '&searchFor='.$searchFor;
		else
			$this->searchFor = '';
	
		// Assign the items per pag variable
		if (!empty($perPage))
		$this->perPage = $perPage;

		// Assign the pag variable
		$this->pag = get_query_var ('pag');
		if($this->pag == 0)
			$this->pag = 1;

		// Take the length of the array
		$this->length = $total;

		// Get the number of pags
		$this->pags = ceil($this->length / $this->perPage);

		// Calculate the starting point 
		$this->start  = ceil(($this->pag - 1) * $this->perPage);

		// Return the part of the array we have requested
		//return array_slice($array, $this->start, $this->perPage);
		return $this->start;
	}

	function links(){
		// Initiate the links array
		$plinks = array();
		$links = array();
		$slinks = array();

		// Concatenate the get variables to add to the pag numbering string
		$queryURL = '';
		if (count($_GET)) {
			foreach ($_GET as $key => $value) {
			  if ($key != 'pag')
				if ($key != 'searchFor')
					$queryURL .= '&'.$key.'='.$value;

			}
		}
		
		// If we have more then one pags
		if (($this->pags) > 1){
			// Assign the 'previous pag' link into the array if we are not on the first pag
			if ($this->pag != 1) {
				if ($this->showFirstAndLast) {
				$plinks[] = '<a href="?pag=1'.esc_attr( $queryURL.$this->searchFor ).'" class="pagLink_fist">'.$this->first.'</a>';
				}
				$plinks[] = '&nbsp;<a href="?pag='. esc_attr( ($this->pag - 1).$queryURL.$this->searchFor ) .'"  class="pagLink_previous">'.$this->prev.'</a>&nbsp;';
			}

			// Assign all the pag numbers & links to the array
			for ($j = 1; $j < ($this->pags + 1); $j++) {
				if ($this->pag == $j) {
					$links[] = ' <a class="selected">'.$j.'</a> '; // If we are on the same pag as the current item
				} else {
					$links[] = ' <a href="?pag='.$j.$queryURL.$this->searchFor.'"  class="pagLink_'.$j.'">'.$j.'</a> '; // add the link to the array
				}
			}
			
			// Eliminate redundant data (links)
			$elementNo = count($links);

			if ($elementNo > 5){
				$middle = round(($this->currentPage + $elementNo)/2  + 0.5);
				if ($this->currentPage > 3)
					for ($i=0; $i<$this->currentPage - 4; $i++)
						unset ($links[$i]);
						
					if ($this->currentPage > 3)
						$links[$i] = '...';
					
					if ($this->currentPage < $elementNo - 2)
						$links[$this->currentPage + 2] = '...';
					
					
					for ($i=$this->currentPage + 3; $i<$elementNo; $i++){
						if ($i != $middle)
							unset ($links[$i]);
					}
					
					if ($this->currentPage < $elementNo - 3)
						$links[$i] = '...';
			}

			// Assign the 'next pag' if we are not on the last pag
			if ($this->pag < $this->pags) {
				$slinks[] = '&nbsp;<a href="?pag='.($this->pag + 1).$queryURL.$this->searchFor.'"  class="pagLink_next">'.$this->next.'</a>&nbsp;';
				if ($this->showFirstAndLast) {
					$slinks[] = '<a href="?pag='.($this->pags).$queryURL.$this->searchFor.'"  class="pagLink_last">'.$this->last.'</a>';
				}
			}
			
			// Push the array into a string using any some glue
			return implode(' ', $plinks).implode($this->implodeBy, $links).implode(' ', $slinks);
		}
		return;
	}
}