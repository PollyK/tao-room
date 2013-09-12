<?php

Class Category_model extends CI_Model {

    function get_categories($parent = false) {
        if ($parent !== false) {
            $this->db->where('parent_id', $parent);
        }
        //$this->db->select('id');
        $this->db->order_by('categories.sequence', 'ASC');

        //this will alphabetize them if there is no sequence
        $this->db->order_by('name', 'ASC');
        $result = $this->db->get('categories');
//        var_dump($result->result());die;
//        $categories = array();
//        foreach ($result->result() as $cat) {
//            $categories[] = $this->get_category($cat->id);
//        }

        return $result->result();
    }

    function get_category_tree($last_node_id) {
        $sql = 'SELECT T2.id, T2.*
FROM (
    SELECT
        @r AS _id,
        (SELECT @r := parent_id FROM  system_categories WHERE id = _id) AS parent_id,
        @l := @l + 1 AS lvl
    FROM
        (SELECT @r := ' . $last_node_id . ', @l := 0) vars,
        system_categories m
    WHERE @r <> 0) T1
JOIN system_categories T2
ON T1._id = T2.id
ORDER BY T1.lvl DESC';
        $result = $this->db->query($sql);
        if ($result) {
            return $result->result();
        }
        return false;
    }

    //this is for building a menu
    function get_categories_tierd($parent = 0) {
        $categories = array();
        $result = $this->get_categories($parent);
        foreach ($result as $category) {
            $categories[$category->id]['category'] = $category;

            $tierd = $this->get_categories_tierd($category->id);
            $categories[$category->id]['children'] = $tierd;
        }
        return $categories;
    }

    function category_autocomplete($name, $limit) {
        return $this->db->like('name', $name)->get('categories', $limit)->result();
    }

    function get_category($id) {
        return $this->db->get_where('categories', array('id' => $id))->row();
    }

    function get_category_human_name_by_slug($slug_name) {
        $result = $this->db->get_where('categories', array('slug' => $slug_name))->row();
        if ($result) {
            return $result->name;
        } else {
            return $slug_name;
        }
    }

    function get_category_products_admin($id) {
        $this->db->order_by('sequence', 'ASC');
        $result = $this->db->get_where('category_products', array('category_id' => $id));
        $result = $result->result();

        $contents = array();
        foreach ($result as $product) {
            $result2 = $this->db->get_where('products', array('id' => $product->product_id));
            $result2 = $result2->row();

            $contents[] = $result2;
        }

        return $contents;
    }

    function get_category_products($id, $limit, $offset) {
        $this->db->order_by('sequence', 'ASC');
        $result = $this->db->get_where('category_products', array('category_id' => $id), $limit, $offset);
        $result = $result->result();

        $contents = array();
        $count = 1;
        foreach ($result as $product) {
            $result2 = $this->db->get_where('products', array('id' => $product->product_id));
            $result2 = $result2->row();

            $contents[$count] = $result2;
            $count++;
        }

        return $contents;
    }

    function organize_contents($id, $products) {
        //first clear out the contents of the category
        $this->db->where('category_id', $id);
        $this->db->delete('category_products');

        //now loop through the products we have and add them in
        $sequence = 0;
        foreach ($products as $product) {
            $this->db->insert('category_products', array('category_id' => $id, 'product_id' => $product, 'sequence' => $sequence));
            $sequence++;
        }
    }

    function save($category, $ignore_id = false) {
        if ($category['id'] && !$ignore_id) {
            $this->db->where('id', $category['id']);
            $this->db->update('categories', $category);

            return $category['id'];
        } else {
            if ($ignore_id) {
                $exists = $this->get_category($category['id']);
                if ($exists) {
                    $this->db->where('id', $category['id']);
                    $this->db->delete('categories');
                }
            }
            $this->db->insert('categories', $category);
            return $this->db->insert_id();
        }
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('categories');

        //delete references to this category in the product to category table
        $this->db->where('category_id', $id);
        $this->db->delete('category_products');
    }

}