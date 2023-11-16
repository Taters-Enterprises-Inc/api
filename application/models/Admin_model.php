<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model 
{
    public function __construct(){
        $this->bsc_db = $this->load->database('bsc', TRUE, TRUE);
    }

    public function getCustomerFeedBackAveragePerRatingsGroupsQuestion($question_id, $group_id, $store_id, $startDate, $endDate){
        $this->bsc_db->select('E.lowest_rate, E.highest_rate,AVG(A.rate) as avg');
            
        $this->bsc_db->from('customer_survey_response_ratings A');
        $this->bsc_db->join('survey_question_ratings B', 'B.id = A.survey_question_rating_id','left');
        $this->bsc_db->join('survey_questions C', 'C.id = B.survey_question_id','left');
        $this->bsc_db->join('survey_question_offered_ratings D', 'D.id = B.survey_question_offered_rating_id','left');
        $this->bsc_db->join('survey_question_offered_rating_groups E', 'E.id = D.survey_question_offered_rating_group_id','left');
        $this->bsc_db->join('customer_survey_responses F', 'F.id = A.customer_survey_response_id','left');

        $this->bsc_db->where('C.id', $question_id);
        $this->bsc_db->where('D.survey_question_offered_rating_group_id', $group_id);
        $this->bsc_db->where('F.store_id', $store_id);

        $this->bsc_db->where('F.dateadded >=', $startDate);
        $this->bsc_db->where('F.dateadded <=', $endDate);

        $this->bsc_db->group_by('C.survey_section_id');

        $query = $this->bsc_db->get();
        return $query->row();
    }

    public function getSurveyQuestionSectionQuestions($section_id){
        $this->bsc_db->select('A.id, A.description as question_name');
            
        $this->bsc_db->from('survey_questions A');
        $this->bsc_db->where('A.survey_section_id', $section_id);

        $query = $this->bsc_db->get();
        return $query->result();
    }

    public function getSurveyQuestionOfferedRatingGroups(){
        $this->bsc_db->select('A.id, A.name,');
            
        $this->bsc_db->from('survey_question_offered_rating_groups A');

        $query = $this->bsc_db->get();
        return $query->result();
    }

    public function getCustomerFeedBackAveragePerRatingsGroups($section_id, $group_id, $store_id, $startDate, $endDate){
        $this->bsc_db->select('E.lowest_rate, E.highest_rate,AVG(A.rate) as avg');
            
        $this->bsc_db->from('customer_survey_response_ratings A');
        $this->bsc_db->join('survey_question_ratings B', 'B.id = A.survey_question_rating_id','left');
        $this->bsc_db->join('survey_questions C', 'C.id = B.survey_question_id','left');
        $this->bsc_db->join('survey_question_offered_ratings D', 'D.id = B.survey_question_offered_rating_id','left');
        $this->bsc_db->join('survey_question_offered_rating_groups E', 'E.id = D.survey_question_offered_rating_group_id','left');
        $this->bsc_db->join('customer_survey_responses F', 'F.id = A.customer_survey_response_id','left');

        $this->bsc_db->where('C.survey_section_id', $section_id);
        $this->bsc_db->where('D.survey_question_offered_rating_group_id', $group_id);
        $this->bsc_db->where('F.store_id', $store_id);

        $this->bsc_db->where('F.dateadded >=', $startDate);
        $this->bsc_db->where('F.dateadded <=', $endDate);

        $this->bsc_db->group_by('C.survey_section_id');

        $query = $this->bsc_db->get();
        return $query->row();
    }

    public function getSurveyQuestionSections(){
        $this->bsc_db->select('A.id, A.name,');
            
        $this->bsc_db->from('survey_question_sections A');

        $query = $this->bsc_db->get();
        return $query->result();
    }

    public function getDashboardShopFeaturedProducts($store){
        $this->db->select('
            CONCAT(B.product_label," ",C.name) as product_name,
            C.product_image,
            C.price,
            count(*) as purchased,
        ');
            
        $this->db->from('transaction_tb A');
        $this->db->join('order_items B', 'B.transaction_id = A.id', 'left');
        $this->db->join('products_tb C', 'C.id = B.product_id', 'left');

        $this->db->group_by("product_name");
        $this->db->where('C.name IS NOT NULL');
        $this->db->where('A.status', 6);

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $this->db->order_by('purchased', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function getDashboardShopMobileUsersCount($store){
        $this->db->distinct();

        $this->db->select('C.id, C.first_name');
            
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id', 'left');
        $this->db->join('mobile_users C', 'C.id = B.mobile_user_id');

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDashboardShopFbUsersCount($store){
        $this->db->distinct();

        $this->db->select('C.id, C.first_name');
            
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id', 'left');
        $this->db->join('fb_users C', 'C.id = B.fb_user_id');

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDashboardShopInitialCheckoutsCount( $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('snackshop_initial_checkout_logs A');

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getDashboardShopProductViewsCount( $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('snackshop_product_view_logs A');

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->row()->all_count;
    }


    public function getDashboardShopAddToCartsCount( $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('snackshop_add_to_cart_logs A');

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getDashboardShopCompletedTransactionCount( $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('transaction_tb A');

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $this->db->where('A.status', 6);

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getDashboardShopTransactionsCount( $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('transaction_tb A');

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function updateInfluencerPayable($influencer_id, $payable){
		$this->db->set('payable', $payable);
        $this->db->where('influencer_id', $influencer_id);
        $this->db->update("influencer_profiles");
    }

    public function changeStatusInfluencerCashout($influencer_cashout_id, $status){
		$this->db->set('influencer_cashout_status_id', $status);
        $this->db->where('id', $influencer_cashout_id);
        $this->db->update("influencer_cashouts");
    }

    public function getInfluencerCashoutById($influencer_cashout_id){
        $this->db->select('
            A.id,
            A.influencer_id,
            A.cashout,
            A.influencer_cashout_status_id,
            A.dateadded,
            B.id_number,
            B.first_name,
            B.middle_name,
            B.last_name,
            B.id_front,
            B.id_back,
            B.fb_user_id,
            B.mobile_user_id,
            B.payment_selected,
            B.account_number,
            B.account_name,
            CONCAT(C.first_name," ",C.last_name) as fb_user_name,
            CONCAT(D.first_name," ",D.last_name) as mobile_user_name,
            E.payable,
        ');
        $this->db->from('influencer_cashouts A');
        $this->db->join('influencers B','B.id = A.influencer_id');
        $this->db->join('fb_users C', 'C.id = B.fb_user_id', 'left');
        $this->db->join('mobile_users D', 'D.id = B.mobile_user_id', 'left');
        $this->db->join('influencer_profiles E','E.influencer_id = A.influencer_id');

        $this->db->where('A.id', $influencer_cashout_id);

        $query = $this->db->get();
        return $query->row();
    }

    
    public function getInfluencerCashoutsCount($status, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('influencer_cashouts A');
        $this->db->join('influencers B','B.id = A.influencer_id');
        $this->db->join('fb_users C', 'C.id = B.fb_user_id', 'left');
        $this->db->join('mobile_users D', 'D.id = B.mobile_user_id', 'left');


        if($status)
            $this->db->where('A.influencer_cashout_status_id', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('A.cashout', $search);
            $this->db->or_like('CONCAT(D.first_name," ",D.last_name)', $search);
            $this->db->or_like('CONCAT(C.first_name," ",C.last_name)', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getInfluencerCashouts($row_no, $row_per_page, $status, $order_by,  $order, $search){
        $this->db->select('
            A.id,
            A.cashout,
            A.influencer_cashout_status_id,
            A.dateadded,
            CONCAT(C.first_name," ",C.last_name) as fb_user_name,
            CONCAT(D.first_name," ",D.last_name) as mobile_user_name,
        ');

        $this->db->from('influencer_cashouts A');
        $this->db->join('influencers B','B.id = A.influencer_id');
        $this->db->join('fb_users C', 'C.id = B.fb_user_id', 'left');
        $this->db->join('mobile_users D', 'D.id = B.mobile_user_id', 'left');

        if($status)
            $this->db->where('A.influencer_cashout_status_id', $status);


        if($search){
            $this->db->group_start();
            $this->db->like('A.cashout', $search);
            $this->db->or_like('CONCAT(D.first_name," ",D.last_name)', $search);
            $this->db->or_like('CONCAT(C.first_name," ",C.last_name)', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

    function insertInfluencerPromo($data){
        $this->db->trans_start();
		$this->db->insert('influencer_promos', $data);
        $this->db->trans_complete();
    }

    function getSettingInfluencers(){

        $this->db->select('
            A.id,
            CONCAT(B.first_name," ",B.last_name) as fb_user_name,
            CONCAT(C.first_name," ",C.last_name) as mobile_user_name,
        ');

        $this->db->from('influencers A');
        $this->db->join('fb_users B', 'B.id = A.fb_user_id', 'left');
        $this->db->join('mobile_users C', 'C.id = A.mobile_user_id', 'left');

        $this->db->where('A.status', 9);

        $query = $this->db->get();
        return $query->result();
    }

    public function getInfluencerPromosCount($search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('influencer_promos A');
        $this->db->join('influencers B', 'B.id = A.influencer_id');
        $this->db->join('fb_users C', 'C.id = B.fb_user_id', 'left');
        $this->db->join('mobile_users D', 'D.id = B.mobile_user_id', 'left');

        if($search){
            $this->db->group_start();
            $this->db->like('A.referral_code', $search);
            $this->db->or_like('CONCAT(C.first_name," ",C.last_name)', $search);
            $this->db->or_like('CONCAT(D.first_name," ",D.last_name)', $search);
            $this->db->or_like('A.customer_discount', $search);
            $this->db->or_like('A.influencer_discount', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }
    
    function getInfluencerPromos($row_no, $row_per_page, $order_by,  $order, $search){
        $this->db->select('
            A.id,
            A.referral_code,
            A.customer_discount,
            A.influencer_discount,
            A.dateadded,
            CONCAT(C.first_name," ",C.last_name) as fb_user_name,
            CONCAT(D.first_name," ",D.last_name) as mobile_user_name,
        ');

        $this->db->from('influencer_promos A');
        $this->db->join('influencers B', 'B.id = A.influencer_id');
        $this->db->join('fb_users C', 'C.id = B.fb_user_id', 'left');
        $this->db->join('mobile_users D', 'D.id = B.mobile_user_id', 'left');

        if($search){
            $this->db->group_start();
            $this->db->like('A.referral_code', $search);
            $this->db->or_like('CONCAT(C.first_name," ",C.last_name)', $search);
            $this->db->or_like('CONCAT(D.first_name," ",D.last_name)', $search);
            $this->db->or_like('A.customer_discount', $search);
            $this->db->or_like('A.influencer_discount', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    

    function getInfluencerById($influencer_id){
        $this->db->select('
            id,
            fb_user_id,
            mobile_user_id,
        ');

        $this->db->from('influencers');

        $this->db->where('id', $influencer_id);

        $query = $this->db->get();
        return $query->row();
    }

    function getDiscountUserById($discount_user_id){
        $this->db->select('
            id,
            fb_user_id,
            mobile_user_id,
        ');

        $this->db->from('discount_users');

        $this->db->where('id', $discount_user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getInfluencerByFbOrMobileUser($fb_user_id, $mobile_user_id){
        $this->db->select('
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.id_front,
            A.id_back,
            A.status,
        ');

        $this->db->from('influencers A');
        $this->db->join('influencer_profiles B', 'B.influencer_id = A.id');
        
        if(isset($fb_user_id)){
            $this->db->where('A.fb_user_id', $fb_user_id);
        }elseif(isset($mobile_user_id)){
            $this->db->where('A.mobile_user_id', $mobile_user_id);
        }

        $query = $this->db->get();
        return $query->row();
    }

    function getInfluencerProfile($influencer_id){

        $this->db->select('payable');

        $this->db->from('influencer_profiles');
        $this->db->where('influencer_id', $influencer_id);

        $query = $this->db->get();

        return $query->row();
    }

    function updateInfluencerProfilePayable($influencer_id, $payable){
        $this->db->set('payable',$payable);
        $this->db->where("influencer_id", $influencer_id);
        $this->db->update("influencer_profiles");
    }

    function getTransactionById($transaction_id){
        $this->db->select('
            A.hash_key,
            A.influencer_discount,
            B.fb_user_id,
            B.mobile_user_id,
            D.influencer_id,
        ');

        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('influencer_promos D', 'D.id = A.influencer_promo_id', 'left');

        $this->db->where('A.id', $transaction_id);

        $query = $this->db->get();

        return $query->row();
    }

    function insertInfluencerDeal($data){
        $this->db->trans_start();
		$this->db->insert('influencer_deals', $data);
        $this->db->trans_complete();
    }

    function getInfluencersId(){
        $this->db->select('id, fb_user_id, mobile_user_id ');

        $this->db->from('influencers');

        $query = $this->db->get();

        return $query->result();
    }


    function updatePopclubDealStatus($deal_id, $status){
        $this->db->set('status', $status);
        $this->db->where("id", $deal_id);
        $this->db->update("dotcom_deals_tb");
    }

    
    function insertDealRegionDaLog($data){
        $this->db->trans_start();
		$this->db->insert('deals_region_da_log', $data);
        $this->db->trans_complete();
    }


    function removeDealsRegionDaLogById($da_log_id){
        $this->db->where('id', $da_log_id);
		$this->db->delete('deals_region_da_log');
    }


    function removeDealsRegionDaLogByDealId($deal_id){
        $this->db->where('deal_id', $deal_id);
		$this->db->delete('deals_region_da_log');
    }

    function getDealsRegionDaLogByDealId($deal_id){
        $this->db->select('id, store_id');

        $this->db->from('deals_region_da_log');

        $this->db->where('deal_id', $deal_id);

        $query = $this->db->get();

        return $query->result();
    }

    function removePopclubProductIncludeObtainable($product_include_obtainable_id){
        $this->db->where('id', $product_include_obtainable_id);
        $this->db->delete('deals_product_promo_include_obtainable');
    }

    function removePopclubProductInclude($product_include_id){
        $this->db->where('id', $product_include_id);
        $this->db->delete('deals_product_promo_include');
    }
    
    function removeDealsProductWithVariants($deal_id){
        $this->db->where('deal_id', $deal_id);
		$this->db->delete('dotcom_deals_product_tb');
    }

    function removePopclubProductExclude($deal_id){
        $this->db->where('deal_id', $deal_id);
		$this->db->delete('deals_product_promo_exclude');
    }

    function removePlatformCombinations($deal_id){
        $this->db->where('deal_id', $deal_id);
		$this->db->delete('dotcom_deals_platform_combination');
    }

    function updatePopclubDeal($deal_id, $data){
        $this->db->where('id', $deal_id);
        $this->db->update('dotcom_deals_tb', $data);
    }

    public function changeStatusInfluencer($influencer_user_id, $status){
		$this->db->set('status', (int) $status);
        $this->db->where('id', $influencer_user_id);
        $this->db->update("influencers");
    }

    public function getInfluencerApplication($influencer_user_id){
        $this->db->select("
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.dateadded,
            A.id_front,
            A.id_back,
            A.payment_selected,
            A.account_number,
            A.account_name,
            A.contract,
            A.status,

            B.first_name as fb_first_name,
            B.last_name as fb_last_name,

            C.first_name as mobile_first_name,
            C.last_name as mobile_last_name,
        ");
        $this->db->from('influencers A');
        $this->db->join('fb_users B', 'B.id = A.fb_user_id','left');
        $this->db->join('mobile_users C', 'C.id = A.mobile_user_id','left');

        $this->db->where('A.id', $influencer_user_id);

        $query = $this->db->get();
        return $query->row();
    }


    public function getInfluencerApplications($row_no, $row_per_page, $status, $order_by,  $order, $search){
        
        $this->db->select("
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.dateadded,
            A.status,
        ");
        $this->db->from('influencers A');
        
            
        if($status)
            $this->db->where('A.status', $status);


        if($search){
            $this->db->group_start();
            $this->db->like('A.id_number', $search);
            $this->db->or_like('A.first_name', $search);
            $this->db->or_like('A.middle_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getInfluencerApplicationsCount($status, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('influencers A');

        if($status)
            $this->db->where('A.status', $status);

            
        if($search){
            $this->db->group_start();
            $this->db->like('A.id_number', $search);
            $this->db->or_like('A.first_name', $search);
            $this->db->or_like('A.middle_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function getPopclubDealStores($deal_id){

        $this->db->select('
            A.store_id,
			C.region_store_id,
            CONCAT(B.name , " (" , D.name ," - ", E.name , ")") as name,
        ');

        $this->db->from('deals_region_da_log A');
        $this->db->join('store_tb B', 'B.store_id = A.store_id');
		$this->db->join('region_store_combination_tb C', 'C.region_store_id = B.region_store_combination_id');
        $this->db->join('dotcom_deals_category D', 'D.id = A.platform_category_id');
        $this->db->join('dotcom_deals_platform E','E.id = D.dotcom_deals_platform_id');

        $this->db->where('A.deal_id',$deal_id);
        
        $query_shop_product_stores = $this->db->get();
        return $query_shop_product_stores->result();
    }


    function getPopclubDealCategories($deal_id){
        $this->db->select('
            B.id,
            B.name,
            C.name as platform_name,
        ');

        $this->db->from('dotcom_deals_platform_combination A');
        $this->db->join('dotcom_deals_category B','B.id = A.platform_category_id');
        $this->db->join('dotcom_deals_platform C','C.id = B.dotcom_deals_platform_id');
        $this->db->where('A.deal_id',$deal_id);

        $query = $this->db->get();
        return $query->result();
    }
    

    function getPopclubDealIncludedProductObtainable($include_product_id){
        $this->db->select('
            A.id,
            A.quantity,
            A.product_id,
            A.product_variant_option_tb_id,
            A.promo_discount_percentage,
            B.name as product_name,
            C.name as variant_name,
        ');

        $this->db->from('deals_product_promo_include_obtainable A');
        $this->db->join('products_tb B','B.id = A.product_id');
        $this->db->join('product_variant_options_tb C','C.id = A.product_variant_option_tb_id', 'left');
        $this->db->where('A.deals_product_promo_include_id',$include_product_id);

        $query = $this->db->get();
        return $query->result();
    }

    function getPopclubDealIncludedProducts($deal_id){
        $this->db->select('
            A.id,
            A.deal_id,
            A.product_id,
            A.product_variant_option_tb_id, 
            A.promo_discount_percentage,
            A.quantity,
            B.name as product_name,
            C.name as variant_name,
        ');

        $this->db->from('deals_product_promo_include A');
        $this->db->join('products_tb B','B.id = A.product_id');
        $this->db->join('product_variant_options_tb C','C.id = A.product_variant_option_tb_id', 'left');
        $this->db->where('A.deal_id',$deal_id);

        $query = $this->db->get();
        return $query->result();
    }
    

    function getPopclubDealExcludedProducts($deal_id){
        $this->db->select('
            B.id,
            B.name, 
        ');

        $this->db->from('deals_product_promo_exclude A');
        $this->db->join('products_tb B','B.id = A.product_id');
        $this->db->where('A.deal_id',$deal_id);

        $query = $this->db->get();
        return $query->result();
    }

    function getPopclubDealProducts($deal_id){
        $this->db->select('
            A.id,
            A.deal_id,
            A.product_id,
            A.product_variant_options_id, 
            A.quantity,
            B.name as product_name,
            C.name as variant_name,
        ');

        $this->db->from('dotcom_deals_product_tb A');
        $this->db->join('products_tb B','B.id = A.product_id');
        $this->db->join('product_variant_options_tb C','C.id = A.product_variant_options_id', 'left');
        $this->db->where('A.deal_id',$deal_id);

        $query = $this->db->get();
        return $query->result();
    }
    
    function getPopclubDeal($deal_id){
        $this->db->select('
            A.id,
            A.alias,
            A.name,
            A.product_image,
            A.original_price,
            A.promo_price,
            A.promo_discount_percentage,
            A.minimum_purchase,
            A.is_free_delivery,
            A.description,
            A.seconds_before_expiration,
            A.available_start_time,
            A.available_end_time,
            A.available_start_datetime,
            A.available_end_datetime,
            A.available_days,
            A.status,
            A.hash,
        ');

        $this->db->from('dotcom_deals_tb A');
        $this->db->where('A.id', $deal_id);

        $query_product = $this->db->get();
        return $query_product->row();
    }


    function insertDealRegionDaLogs($data){
        $this->db->trans_start();
		$this->db->insert_batch('deals_region_da_log', $data);
        $this->db->trans_complete();
    }


    function insertDealsProductsIncludeObtainable($data){
        $this->db->trans_start();
		$this->db->insert_batch('deals_product_promo_include_obtainable', $data);
        $this->db->trans_complete();
    }

    function insertDealsProductsPromoInclude($data){
        $this->db->trans_start();
		$this->db->insert('deals_product_promo_include', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    function insertDealsProductsWithVariants($data){
        $this->db->trans_start();
		$this->db->insert_batch('dotcom_deals_product_tb', $data);
        $this->db->trans_complete();
    }

    function insertDealsProductPromoExclude($data){
        $this->db->trans_start();
		$this->db->insert_batch('deals_product_promo_exclude', $data);
        $this->db->trans_complete();
    }

    function insertDealsPlatformCombinations($data){
        $this->db->trans_start();
		$this->db->insert_batch('dotcom_deals_platform_combination', $data);
        $this->db->trans_complete();
    }

    function insertPopclubDeal($data){
        $this->db->trans_start();
		$this->db->insert('dotcom_deals_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    public function getSettingDealStoresPopclub(){
        $this->db->select('
            A.store_id,
            A.name,
            B.name as menu_name,
			C.region_store_id,
        ');
        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
		$this->db->join('region_store_combination_tb C', 'C.region_store_id = A.region_store_combination_id');

        $this->db->group_start();
        $this->db->where('A.popclub_walk_in_status', 1);
        $this->db->or_where('A.popclub_online_delivery_status', 1);
        $this->db->group_end();

        $query = $this->db->get();
        return $query->result();
    }

    public function getProductSizeId($product_id){
        $this->db->select('
            A.id,
            A.name
        ');

        $this->db->from('product_variants_tb A');

        $this->db->where('A.name', 'size');
        $this->db->where('A.product_id', $product_id);

        $query_shop_products = $this->db->get();
        return $query_shop_products->row();
    }

    public function getAdminSettingDealShopProducts(){
        $this->db->select('
            A.id,
            A.name,
        ');

        $this->db->from('products_tb A');
        $this->db->where('A.popclub_status',1);

        $query_shop_products = $this->db->get();
        return $query_shop_products->result();
    }

    public function getPopclubCategories(){
        $this->db->select('
            A.id,
            A.name,
            B.name as platform_name,
        ');

        $this->db->from('dotcom_deals_category A');
        $this->db->join('dotcom_deals_platform B', 'B.id = A.dotcom_deals_platform_id');

        $query_popclub_categories = $this->db->get();
        return $query_popclub_categories->result();
    }

    public function getPopclubDealsCount($status, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('dotcom_deals_tb');

        if($status)
            $this->db->where('status', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('original_price', $search);
            $this->db->or_like('promo_price', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }


    function getPopclubDeals($row_no, $row_per_page, $status, $order_by,  $order, $search){
        $this->db->select('
            id,
            product_image,
            name,
            original_price,
            promo_price,
            description,
            status,
        ');

        $this->db->from('dotcom_deals_tb');
    
        if($status)
            $this->db->where('status', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('original_price', $search);
            $this->db->or_like('promo_price', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    
    function updateCateringPackageStatus($package_id, $status){
        $this->db->set('status', $status);
        $this->db->where("id", $package_id);
        $this->db->update("catering_packages_tb");
    }

    function removePackageDynamicPrices($package_id){
        $this->db->where('package_id', $package_id);
		$this->db->delete('catering_package_prices_tb');
    }

    function removePackageVariantOption($package_variant_option_id){
        $this->db->where('id', $package_variant_option_id);
        $this->db->delete('catering_package_variant_options_tb');
    }
    
    function removePackageVariant($package_variant_id){
        $this->db->where('id', $package_variant_id);
        $this->db->delete('catering_package_variants_tb');
    }
    
    function getPackageVariants($package_id){
        $this->db->select('id');

        $this->db->from('catering_package_variants_tb');
        $this->db->where('product_id', $package_id);
        
        $query_package_variants = $this->db->get();

        return $query_package_variants->result();
    }

    function getPackageVariantOptions($package_variant_id){
        $this->db->select('id');

        $this->db->from('catering_package_variant_options_tb');
        $this->db->where('product_variant_id', $package_variant_id);
        
        $query_package_variant_options = $this->db->get();

        return $query_package_variant_options->result();
    }

    

    function removeCateringRegionDaLogById($catering_region_da_log_id){
        $this->db->where('id', $catering_region_da_log_id);
		$this->db->delete('catering_region_da_log');
    }

    function removeCateringRegionDaLogByPackageId($package_id){
        $this->db->where('product_id', $package_id);
		$this->db->delete('catering_region_da_log');
    }

    function getCateringRegionDaLogByPackageId($package_id){
        $this->db->select('id, store_id');

        $this->db->from('catering_region_da_log');

        $this->db->where('product_id', $package_id);

        $query = $this->db->get();

        return $query->result();
    }


    function updateCateringPackageCategory($package_id, $data){
        $this->db->where('product_id', (int)$package_id);
        $this->db->update('catering_package_category_tb', $data);
    }

    function updateCateringPackage($package_id, $data){
        $this->db->where('id', $package_id);
        $this->db->update('catering_packages_tb', $data);
    }

    function getCateringPackageDynamicPrices($package_id){
        $this->db->select('id, price, min_qty');

        $this->db->from('catering_package_prices_tb');

        $this->db->where('package_id',$package_id);
        
        $query_catering_package_dynamic_prices = $this->db->get();
        return $query_catering_package_dynamic_prices->result();

    }

    function getCateringPackageStores($package_id){

        $this->db->select('
            A.store_id,
            B.name,
			C.region_store_id,
        ');

        $this->db->from('catering_region_da_log A');
        $this->db->join('store_tb B', 'B.store_id = A.store_id');
		$this->db->join('region_store_combination_tb C', 'C.region_store_id = B.region_store_combination_id');

        $this->db->where('A.product_id',$package_id);
        
        $query_catering_package_stores = $this->db->get();
        return $query_catering_package_stores->result();
    }

    function getCateringPackageVariantOptions($product_variant_id){
        $this->db->select('
            A.name
        ');

        $this->db->from('catering_package_variant_options_tb A');
        $this->db->where('A.product_variant_id',$product_variant_id);

        $query_catering_package_variant_options = $this->db->get();
        return $query_catering_package_variant_options->result();
    }
    
    function getCateringPackageVariants($package_id){
        $this->db->select('
            A.id,
            A.product_id,
            A.name, 
            A.status
        ');

        $this->db->from('catering_package_variants_tb A');
        $this->db->where('A.product_id',$package_id);

        $query_catering_package_variants = $this->db->get();
        return $query_catering_package_variants->result();
    }

    function getCateringPackage($package_id){
        $this->db->select('
            A.id,
            A.name,
            A.product_image,
            A.description,
            A.delivery_details,
            A.price,
            A.uom,
            A.add_details,
            A.status,
            A.category,
            A.num_flavor,
            A.dateadded,
            A.free_threshold,
        ');

        $this->db->from('catering_packages_tb A');
        $this->db->where('A.id', $package_id);

        $query_package = $this->db->get();
        return $query_package->row();
    }

    function insertCateringPackageCategory($data){
        $this->db->trans_start();
		$this->db->insert('catering_package_category_tb', $data);
        $this->db->trans_complete();
    }

    function insertPackageDynamicPrices($data){
        $this->db->trans_start();
		$this->db->insert_batch('catering_package_prices_tb', $data);
        $this->db->trans_complete();
    }

    function insertCateringPackageVariantOption($data){
        $this->db->trans_start();
		$this->db->insert('catering_package_variant_options_tb', $data);
        $this->db->trans_complete();
    }

    function insertCateringPackageVariant($data){
        $this->db->trans_start();
		$this->db->insert('catering_package_variants_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    function insertCateringRegionDaLogs($data){
        $this->db->trans_start();
		$this->db->insert_batch('catering_region_da_log', $data);
        $this->db->trans_complete();
    }

    function insertCateringPackage($data){
        $this->db->trans_start();
		$this->db->insert('catering_packages_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    public function getCateringPackagesCount($status, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('catering_packages_tb');

        if($status)
            $this->db->where('status', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('price', $search);
            $this->db->or_like('add_details', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function getCateringPackages($row_no, $row_per_page, $status, $order_by,  $order, $search){
        $this->db->select('
            id,
            product_image,
            name,
            description,
            price,
            add_details,
            status,
        ');

        $this->db->from('catering_packages_tb');
    
        if($status)
            $this->db->where('status', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('price', $search);
            $this->db->or_like('add_details', $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    
    function getProductWithAddons($product_id){
        $this->db->select('
            B.id,
            B.name,
        ');

        $this->db->from('product_with_addons A');
        $this->db->join('products_tb B', 'B.id = A.addon_product_id');
        $this->db->where('A.product_id', $product_id);

        $query = $this->db->get();

        return $query->result();
    }
    
    function insertProductWithAddons($data){
        $this->db->trans_start();
		$this->db->insert_batch('product_with_addons', $data);
        $this->db->trans_complete();
    }

    function removeProductWithAddons($product_id){
        $this->db->where('product_id', $product_id);
		$this->db->delete('product_with_addons');
    }

    public function getSettingProductStoresSnackshop(){
        $this->db->select('
            A.store_id,
            A.name,
            B.name as menu_name,
			C.region_store_id,
        ');
        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
		$this->db->join('region_store_combination_tb C', 'C.region_store_id = A.region_store_combination_id');

        $query = $this->db->get();
        return $query->result();
    }

    public function getSettingProductStoresCatering(){
        $this->db->select('
            A.store_id,
            A.name,
            B.name as menu_name,
			C.region_store_id,
        ');
        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
		$this->db->join('region_store_combination_tb C', 'C.region_store_id = A.region_store_combination_id');

        $this->db->where('A.catering_status', 1);

        $query = $this->db->get();
        return $query->result();
    }


    function removeCateringRegionDaLog($catering_region_da_log_id){
        $this->db->where('id', $catering_region_da_log_id);
		$this->db->delete('catering_region_da_log');
    }

    function insertCateringRegionDaLog($data){
        $this->db->trans_start();
		$this->db->insert('catering_region_da_log', $data);
        $this->db->trans_complete();
    }

    function getCateringRegionDaLog($store_id){
        $this->db->select('id, product_id');

        $this->db->from('catering_region_da_log');

        $this->db->where('store_id', $store_id);

        $query = $this->db->get();

        return $query->result();
    }
    
    function insertRegionDaLog($data){
        $this->db->trans_start();
		$this->db->insert('region_da_log', $data);
        $this->db->trans_complete();
    }

    function removeSnackshopRegionDaLogByProductId($productId){
        $this->db->where('product_id', $productId);
		$this->db->delete('region_da_log');
    }


    function removeSnackshopRegionDaLogById($region_da_log_id){
        $this->db->where('id', $region_da_log_id);
		$this->db->delete('region_da_log');
    }

    function getSnackshopRegionDaLogByProductId($product_id){
        $this->db->select('id, store_id');

        $this->db->from('region_da_log');

        $this->db->where('product_id', $product_id);

        $query = $this->db->get();

        return $query->result();
    }


    function getSnackshopRegionDaLog($store_id){
        $this->db->select('id, product_id');

        $this->db->from('region_da_log');

        $this->db->where('store_id', $store_id);

        $query = $this->db->get();

        return $query->result();
    }

    
    function getRegionStoreCombinationById($region_store_combination_id){
        $this->db->select('
            id,
            region_id,
            region_store_id,
        ');
        $this->db->from('region_store_combination_tb');

        $this->db->where('id', $region_store_combination_id);

        $query = $this->db->get();
        return $query->row();
    }

    
    function updateStore($store_id, $data){
        $this->db->where('store_id', $store_id);
        $this->db->update('store_tb', $data);
    }


    function getSettingStoreProductCateringRegionDaLog($store_id){
        $this->db->select('
            B.id,
            B.name,
        ');

        $this->db->from('catering_region_da_log A');
        $this->db->join('catering_packages_tb B', 'B.id = A.product_id');

        $this->db->where('A.store_id', $store_id);

        $query = $this->db->get();

        return $query->result();
    }

    function getSettingStoreProductRegionDaLog($store_id){
        $this->db->select('
            B.id,
            B.name,
        ');

        $this->db->from('region_da_log A');
        $this->db->join('products_tb B', 'B.id = A.product_id');

        $this->db->where('A.store_id', $store_id);

        $query = $this->db->get();

        return $query->result();
    }

    function getSettingStore($store_id){
        $this->db->select('
            A.id,
            A.store_id,
            A.name,
            A.address,
            A.contact_number,
            A.contact_person,
            A.email,
            A.delivery_hours,
            A.operating_hours,
            A.delivery_rate,
            A.minimum_rate,
            A.catering_delivery_rate,
            A.catering_minimum_rate,
            A.store_hash,
            A.active_reseller_region_id,
            A.available_start_time,
            A.available_end_time,
            A.store_menu_type_id,
            A.locale,
            A.region_id,
            A.lat,
            A.lng,
            A.status,
            A.catering_status,
            A.popclub_walk_in_status,
            A.popclub_online_delivery_status,
            A.branch_status,
            A.store_image,
            B.region_store_id,
            B.id as region_store_combination_tb_id,
        ');
        $this->db->from('store_tb A');
		$this->db->join('region_store_combination_tb B', 'B.region_store_id = A.region_store_combination_id', 'left');

        $this->db->where('A.store_id', $store_id);

        $query = $this->db->get();

        return $query->row();
    }

    function getLatestStoreCreated(){
        $this->db->select('store_id');
        $this->db->from('store_tb');

        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->row();
    }


    public function getCustomerSurveyResponse($survey_verification_id){
        $this->bsc_db->select('
            A.id,
            A.transaction_id,
            A.catering_transaction_id,
            A.fb_user_id,
            A.mobile_user_id,
        ');

        $this->bsc_db->from('customer_survey_responses A');
        $this->bsc_db->where('A.id', $survey_verification_id);

        $query_customer_survey_response = $this->bsc_db->get();

        return $query_customer_survey_response->row();
    }

    
    function getLocales(){
        $this->db->select('id, locale_name as name');
        $this->db->from('dotcom_locale_tb');

        $query = $this->db->get();
        return $query->result();
    }
    
    function getRegionStoreCombinations(){
        $this->db->select('
            A.id, 
            B.name as region_name,
            C.name as region_store_name,
        ');
        $this->db->from('region_store_combination_tb A');
        $this->db->join('region_tb B', 'B.id = A.region_id');
        $this->db->join('region_tb C', 'C.id = A.region_store_id');

        $query = $this->db->get();
        return $query->result();
    }
    
    function getActiveResellerRegions(){
        $this->db->select('id, name');
        $this->db->from('region_tb');
        
        $this->db->where('on_reseller_status', 1);

        $query = $this->db->get();
        return $query->result();
    }

    function removeShopStoreRegionDaLogs($store_id){
        $this->db->where('store_id', $store_id);
		$this->db->delete('region_da_log');
    }

    function insertStore($data){
        $this->db->trans_start();
		$this->db->insert('store_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    function getStoreMenus(){
        $this->db->select('id, name');
        $this->db->from('store_menu_tb');

        $query = $this->db->get();
        return $query->result();
    }

    function getSnackshopSales($start_date, $store){
        $this->db->select("purchase_amount, dateadded");
        $this->db->from('transaction_tb');
        $this->db->where('status', 6);

        $this->db->where('DATE(dateadded) >= ', $start_date);
        
        if(!empty($store))
            $this->db->where_in('store', $store);

        $query_transaction = $this->db->get();
        return $query_transaction->result();
    }
    
    function getCateringSales($start_date, $store){
        $this->db->select("purchase_amount, dateadded");
        $this->db->from('catering_transaction_tb');
        $this->db->where('status', 9);

        $this->db->where('DATE(dateadded) >= ', $start_date);
        
        if(!empty($store))
            $this->db->where_in('store', $store);

        $query_transaction = $this->db->get();
        return $query_transaction->result();
    }

    function getPopClubSales($start_date, $store){
        $this->db->select("purchase_amount, dateadded");
        $this->db->from('deals_redeems_tb');
        $this->db->where('status', 6);

        $this->db->where('DATE(dateadded) >= ', $start_date);
        
        if(!empty($store))
            $this->db->where_in('store', $store);

        $query_transaction = $this->db->get();
        return $query_transaction->result();
    }

    function getSnackshopProductFlavors($product_id){
        $this->db->select("B.id,B.name,B.product_variant_id, A.name as parent_name");
        $this->db->from('product_variants_tb A');
        $this->db->join('product_variant_options_tb B', 'B.product_variant_id = A.id','left');
        $this->db->where('A.product_id', $product_id);
        $this->db->where('B.status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    function getCateringPackageFlavors($package_id){
        $this->db->select("B.id,B.name,B.product_variant_id, A.name as parent_name");
        $this->db->from('catering_package_variants_tb A');
        $this->db->join('catering_package_variant_options_tb B', 'B.product_variant_id = A.id','left');
        $this->db->where('A.product_id', $package_id);
        $this->db->where('B.status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    function updateCateringOrderItemRemarks($order_item_id, $remarks){
        $this->db->set('remarks', $remarks);
        $this->db->where("id", $order_item_id);
        $this->db->update("catering_order_items");
    }

    function removeCateringProductAddons($product_addon_id){
        $this->db->where('product_id', $product_addon_id);
		$this->db->delete('catering_product_addons_tb');
    }

    function getDeals(){
        $this->db->select('
            id,
            name,
        ');

        $this->db->from('dotcom_deals_tb');
        $this->db->where('status', 1);

        $query_products = $this->db->get();
        return $query_products->result();
    }

    function getPackages(){
        $this->db->select('
            id,
            name,
        ');

        $this->db->from('catering_packages_tb');
        $this->db->where('status', 1);

        $query_products = $this->db->get();
        return $query_products->result();
    }

    function getProducts(){
        $this->db->select('
            id,
            name,
        ');

        $this->db->from('products_tb');
        $this->db->where('status', 1);

        $query_products = $this->db->get();
        return $query_products->result();
    }
    
    function updateShopProductStatus($product_id, $status, $type){
        switch($type){
            case 'snackshop':
                $this->db->set('status', $status);
                break;
            case 'popclub':
                $this->db->set('popclub_status', $status);
                break;
        }
        $this->db->where("id", $product_id);
        $this->db->update("products_tb");
    }
    
    
    function removeShopProductCategory($product_id){
        $this->db->where('product_id', $product_id);
        $this->db->delete('product_category_tb');
    }

    function removeShopProduct($product_id){
        $this->db->where('id', $product_id);
        $this->db->delete('products_tb');
    }
    
    function removeProductVariantOptionCombination($product_variant_option_combination_id){
        $this->db->where('id', $product_variant_option_combination_id);
        $this->db->delete('product_variant_option_combinations_tb');
    }

    function removeProductSku($product_sku_id){
        $this->db->where('id', $product_sku_id);
        $this->db->delete('product_skus_tb');
    }

    function removeProductVariantOption($product_variant_option_id){
        $this->db->where('id', $product_variant_option_id);
        $this->db->delete('product_variant_options_tb');
    }
    

    function removeProductVariant($product_variant_id){
        $this->db->where('id', $product_variant_id);
        $this->db->delete('product_variants_tb');
    }
    

    function getProductVariants($product_id){
        $this->db->select('id');

        $this->db->from('product_variants_tb');
        $this->db->where('product_id', $product_id);
        
        $query_product_variants = $this->db->get();

        return $query_product_variants->result();
    }
    

    function getProductVariantOptions($product_variant_id){
        $this->db->select('id, name');

        $this->db->from('product_variant_options_tb');
        $this->db->where('product_variant_id', $product_variant_id);
        
        $query_product_variants = $this->db->get();

        return $query_product_variants->result();
    }

    
    function getProductVariantOptionCombinations($product_variant_option_id){
        $this->db->select('id, sku_id');

        $this->db->from('product_variant_option_combinations_tb');
        $this->db->where('product_variant_option_id', $product_variant_option_id);
        
        $query_product_variants = $this->db->get();

        return $query_product_variants->result();
    }

    function removeShopProductVariantOptionCombination($product_id){
        $this->db->where('product_id', $product_id);
		$this->db->delete('product_variant_option_combinations_tb');
    }

    
    function removeShopProductRegionDaLogs($product_id){
        $this->db->where('product_id', $product_id);
		$this->db->delete('region_da_log');
    }


    function updateShopProductCategory($product_id, $data){
        $this->db->where('product_id', (int)$product_id);
        $this->db->update('product_category_tb', $data);
    }
    function updateShopProduct($product_id, $data){
        $this->db->where('id', $product_id);
        $this->db->update('products_tb', $data);
    }

    function getShopProductStores($product_id){

        $this->db->select('
            A.store_id,
            B.name,
			C.region_store_id,
        ');

        $this->db->from('region_da_log A');
        $this->db->join('store_tb B', 'B.store_id = A.store_id');
		$this->db->join('region_store_combination_tb C', 'C.region_store_id = B.region_store_combination_id');

        $this->db->where('A.product_id',$product_id);
        
        $query_shop_product_stores = $this->db->get();
        return $query_shop_product_stores->result();
    }

    function getShopProductVariantOptions($product_variant_id){
        $this->db->select('
            A.name,
            C.sku,
            C.price,
        ');

        $this->db->from('product_variant_options_tb A');
        $this->db->join('product_variant_option_combinations_tb B', 'B.product_variant_option_id = A.id','left');
        $this->db->join('product_skus_tb C', 'C.id = B.sku_id','left');
        $this->db->where('A.product_variant_id',$product_variant_id);

        $query_shop_product_variant_options = $this->db->get();
        return $query_shop_product_variant_options->result();
    }

    function getShopProductVariants($product_id){
        $this->db->select('
            A.id,
            A.product_id,
            A.name, 
            A.status
        ');

        $this->db->from('product_variants_tb A');
        $this->db->where('A.product_id',$product_id);

        $query_shop_product_variants = $this->db->get();
        return $query_shop_product_variants->result();
    }

    function getShopProduct($product_id){
        $this->db->select('
            A.id,
            A.name,
            A.product_image,
            A.description,
            A.delivery_details,
            A.price,
            A.uom,
            A.add_details,
            A.status,
            A.category,
            A.num_flavor,
            A.dateadded,
        ');

        $this->db->from('products_tb A');
        $this->db->where('A.id', $product_id);

        $query_product = $this->db->get();
        return $query_product->row();
    }

    function insertShopProductCategory($data){
        $this->db->trans_start();
		$this->db->insert('product_category_tb', $data);
        $this->db->trans_complete();
    }

    function insertShopProductVariantOptionCombination($data){
        $this->db->trans_start();
		$this->db->insert('product_variant_option_combinations_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return $insert_id;
    }

    function insertShopProductSku($data){
        $this->db->trans_start();
		$this->db->insert('product_skus_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return $insert_id;
    }

    function insertShopProductVariantOption($data){
        $this->db->trans_start();
		$this->db->insert('product_variant_options_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return $insert_id;
    }

    function insertShopProductVariant($data){
        $this->db->trans_start();
		$this->db->insert('product_variants_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    function insertRegionDaLogs($data){
        $this->db->trans_start();
		$this->db->insert_batch('region_da_log', $data);
        $this->db->trans_complete();
    }

    function insertCateringPackageRegionDaLogs($data){
        $this->db->trans_start();
		$this->db->insert_batch('catering_region_da_log', $data);
        $this->db->trans_complete();
    }

    function insertShopProduct($data){
        $this->db->trans_start();
		$this->db->insert('products_tb', $data);
		$insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    function getShopProducts($row_no, $row_per_page, $status, $order_by,  $order, $search){
        $this->db->select('
            id,
            product_image,
            name,
            description,
            price,
            add_details,
            status,
            popclub_status,
        ');

        $this->db->from('products_tb');
    
        if($status)
            $this->db->where('status', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('price', $search);
            $this->db->or_like('add_details', $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getShopProductsCount($status, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('products_tb');

        if($status)
            $this->db->where('status', $status);

        if($search){
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('price', $search);
            $this->db->or_like('add_details', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function getSurvey($survey_id){
        $this->bsc_db->select('
            A.id,
            A.dateadded,
            A.order_date,
            A.status,
            A.fb_user_id,
            A.mobile_user_id,
            B.name as store_name,
            A.invoice_no,
            C.tracking_no as snackshop_tracking_no,
            D.tracking_no as catering_tracking_no,
            E.name as order_type,
        ');
        $this->bsc_db->from('customer_survey_responses A');
        $this->bsc_db->join($this->db->database.'.store_tb B', 'B.store_id = A.store_id');
        $this->bsc_db->join($this->db->database.'.transaction_tb C', 'C.id = A.transaction_id','left');
        $this->bsc_db->join($this->db->database.'.catering_transaction_tb D', 'D.id = A.catering_transaction_id','left');
        $this->bsc_db->join('customer_survey_response_order_types E', 'E.id = A.customer_survey_response_order_type_id');

        $this->bsc_db->where('A.id', $survey_id);

        $query_customer_survey_response = $this->bsc_db->get();
        $customer_survey_response = $query_customer_survey_response->row();

        $this->db->select('first_name, last_name');

        if($customer_survey_response->fb_user_id){
            $this->db->from('fb_users');
            $this->db->where('id',$customer_survey_response->fb_user_id);
        }else if($customer_survey_response->mobile_user_id){
            $this->db->from('mobile_users');
            $this->db->where('id',$customer_survey_response->mobile_user_id);
        }
        
		$query_user = $this->db->get();
		$user = $query_user->row();

        $customer_survey_response->user = $user;

		$this->bsc_db->select('
            A.id, 
            A.text,
            A.others,
            A.customer_survey_response_id,
            B.description as question,
            D.text as answer,
        ');

        $this->bsc_db->from('customer_survey_response_answers A');
        $this->bsc_db->join('survey_questions B', 'B.id = A.survey_question_id', 'left');
        $this->bsc_db->join('survey_question_answers C', 'C.id = A.survey_question_answer_id', 'left');
        $this->bsc_db->join('survey_question_offered_answers D', 'D.id = C.survey_question_offered_answer_id', 'left');
        
        $this->bsc_db->where('A.customer_survey_response_id', $customer_survey_response->id);
        
		$query_customer_survey_response_answers = $this->bsc_db->get();
		$customer_survey_response_answers = $query_customer_survey_response_answers->result();
        
		$this->bsc_db->select('
            A.id, 
            A.others,
            A.customer_survey_response_id,
            A.rate,
            B.description as question,
            C.survey_question_offered_rating_id,
            D.name,
            D.lowest_rate_text,
            D.lowest_rate,
            D.highest_rate_text,
            D.highest_rate,
        ');

        $this->bsc_db->from('customer_survey_response_ratings A');
        $this->bsc_db->join('survey_questions B', 'B.id = A.survey_question_id', 'left');
        $this->bsc_db->join('survey_question_ratings C', 'C.id = A.survey_question_rating_id', 'left');
        $this->bsc_db->join('survey_question_offered_ratings D', 'D.id = C.survey_question_offered_rating_id', 'left');
        
        $this->bsc_db->where('A.customer_survey_response_id', $customer_survey_response->id);
        
        $query_customer_survey_response_ratings = $this->bsc_db->get();
        $customer_survey_response_ratings = $query_customer_survey_response_ratings->result();

        $data = $customer_survey_response;
        
        $data->answers = $customer_survey_response_answers;
        $data->ratings = $customer_survey_response_ratings;
        
        return $data;
    }

    public function getSurveysCount($status, $search, $store){
        $this->bsc_db->select('count(*) as all_count');
            
        $this->bsc_db->from('customer_survey_responses A');
        $this->bsc_db->join($this->db->database.'.store_tb B', 'B.store_id = A.store_id');
        $this->bsc_db->join('user_profile C', 'C.user_id = A.user_id', 'left');
        $this->bsc_db->join($this->db->database.'.transaction_tb D', 'D.id = A.transaction_id','left');
        $this->bsc_db->join($this->db->database.'.catering_transaction_tb E', 'E.id = A.catering_transaction_id','left');
        $this->bsc_db->join('customer_survey_response_order_types G', 'G.id = A.customer_survey_response_order_type_id');

        if($status)
            $this->bsc_db->where('A.status', $status);

        if(!empty($store))
            $this->bsc_db->where_in('A.store_id', $store);

        if($search){
            $this->bsc_db->group_start();
            $this->bsc_db->like('A.invoice_no', $search);
            $this->bsc_db->or_like('B.name', $search);
            $this->bsc_db->or_like('C.first_name', $search);
            $this->bsc_db->or_like('C.last_name', $search);
            $this->bsc_db->or_like('D.tracking_no', $search);
            $this->bsc_db->or_like('E.tracking_no', $search);
            $this->bsc_db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->bsc_db->group_end();
            
        }

        $query = $this->bsc_db->get();
        return $query->row()->all_count;
    }

    public function getSurveys($row_no, $row_per_page, $status, $order_by,  $order, $search, $store){
        
        $this->bsc_db->select("
            A.id,
            A.dateadded,
            A.order_date,
            A.status,
            B.name as store_name,
            C.first_name,
            C.last_name,
            A.invoice_no,
            D.tracking_no as snackshop_tracking_no,
            E.tracking_no as catering_tracking_no,
            G.name as order_type
        ");

        $this->bsc_db->from('customer_survey_responses A');
        $this->bsc_db->join($this->db->database.'.store_tb B', 'B.store_id = A.store_id');
        $this->bsc_db->join('user_profile C', 'C.user_id = A.user_id','left');
        $this->bsc_db->join($this->db->database.'.transaction_tb D', 'D.id = A.transaction_id','left');
        $this->bsc_db->join($this->db->database.'.catering_transaction_tb E', 'E.id = A.catering_transaction_id','left');
        $this->bsc_db->join('customer_survey_response_order_types G', 'G.id = A.customer_survey_response_order_type_id');
 
        if($status)
            $this->bsc_db->where('A.status', $status);

        if(!empty($store))
            $this->bsc_db->where_in('A.store_id', $store);

        if($search){
            $this->bsc_db->group_start();
            $this->bsc_db->like('A.invoice_no', $search);
            $this->bsc_db->or_like('B.name', $search);
            $this->bsc_db->or_like('C.first_name', $search);
            $this->bsc_db->or_like('C.last_name', $search);
            $this->bsc_db->or_like('D.tracking_no', $search);
            $this->bsc_db->or_like('E.tracking_no', $search);
            $this->bsc_db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->bsc_db->group_end();
        }

        $this->bsc_db->limit($row_per_page, $row_no);
        $this->bsc_db->order_by($order_by, $order);

        $query = $this->bsc_db->get();
        return $query->result();
    }

    public function changeStatusSurveyVerification($survey_verification_id, $status){
		$this->bsc_db->set('status', (int) $status);
        $this->bsc_db->where('id', $survey_verification_id);
        $this->bsc_db->update("customer_survey_responses");
    }

    function updateSettingStoreOperatingHours(
        $store_id,
        $available_start_time,
        $available_end_time
    ){
        $this->db->set('available_start_time', $available_start_time);
        $this->db->set('available_end_time', $available_end_time);
        $this->db->where("store_id", $store_id);
        $this->db->update("store_tb");
    }

    function getStore($store_id){
        $this->db->select('
            A.store_id,
            A.name,
            A.available_start_time,
            A.available_end_time,
            B.name as menu_name,
        ');

        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
        $this->db->where('A.store_id', $store_id);

        $query = $this->db->get();
        return $query->row();
    }
    
    function updateSettingStore($store_primary_key, $name_of_field_status, $status){
        switch($name_of_field_status){
            case 'Snackshop':
                $this->db->set('status', $status);
                break;
            case 'Catering':
                $this->db->set('catering_status', $status);
                break;
            case 'PopClub Store Visit':
                $this->db->set('popclub_walk_in_status', $status);
                break;
            case 'PopClub Online Delivery':
                $this->db->set('popclub_online_delivery_status', $status);
                break;
        }
        $this->db->where("id", $store_primary_key);
        $this->db->update("store_tb");
    }
    
    function getSettingStoresCount($search, $store) {
        $this->db->select('count(*) as all_count');

        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
            
        if(!empty($store))
            $this->db->where_in('A.store_id', $store);
    
        if($search){
            $this->db->group_start();
            $this->db->like('A.name', $search);
            $this->db->or_like('B.name', $search);
            $this->db->group_end();
        }
                
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function getSettingStores($row_no, $row_per_page, $order_by, $order, $search, $store) {
        $this->db->select('
            A.store_id,
            A.name,
            A.status,
            A.store_image,
            A.catering_status,
            A.popclub_walk_in_status,
            A.popclub_online_delivery_status,
            A.branch_status,
            A.available_start_time,
            A.available_end_time,
            B.name as menu_name,
        ');

        $this->db->from('store_tb A');
        $this->db->join('store_menu_tb B', 'B.id = A.store_menu_type_id');
            
        if(!empty($store))
            $this->db->where_in('A.store_id', $store);
        
        if($search){
            $this->db->group_start();
            $this->db->like('A.name', $search);
            $this->db->or_like('B.name', $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }
    
    public function get_order_summary($id)
    { 
        $table = "client_tb A";
        $select_column = array("A.fname", "A.lname", "A.email","A.address", "A.contact_number","A.moh","A.payops","B.id", "B.tracking_no","B.purchase_amount","B.distance_price","B.cod_fee","A.moh","A.payops","B.remarks", "B.status","B.dateadded","B.hash_key","B.store", "B.invoice_num","B.reseller_id","B.reseller_discount","B.discount","Z.name AS store_name","Z.address AS store_address","Z.contact_number AS store_contact","Z.contact_person AS store_person","Z.email AS store_email","Z.delivery_rate AS delivery_rate","Z.moh_notes AS moh_notes","Z.moh_setup AS moh_setup","B.payment_proof","A.add_name","A.add_contact","A.add_address","V.discount_value","V.voucher_code");
        $join_A = "A.id = B.client_id";
        $this->db->select($select_column);  
        $this->db->from($table);
        $this->db->join('transaction_tb B', $join_A ,'left');
        $this->db->join('store_tb Z', 'Z.store_id = B.store' ,'left');
        $this->db->join('voucher_logs_tb V', 'V.transaction_id = B.id' ,'left');
        $this->db->where('B.id', $id);
        $query_info = $this->db->get();
        $info = $query_info->result();

        $this->db->from('products_tb P');
        $this->db->select('
            P.id,
            P.name,
            P.price,
            P.add_details,
            P.add_remarks,
            P.dateadded,
            O.product_id,
            O.transaction_id,
            O.quantity,
            O.remarks,
            O.promo_id,
            O.promo_price,
            O.product_price,
            O.product_label,
            O.addon_base_product,
            O.addon_base_product_name,
            O.deal_discount_percentage,
            D.alias,
            D.promo_discount_percentage
        ');
        $this->db->join('order_items O', 'P.id = O.product_id' ,'left');
        $this->db->join('dotcom_deals_tb D', 'D.id = O.deal_id' ,'left');
        $this->db->where('O.transaction_id', $id);
        $query_orders = $this->db->get();
        $orders = $query_orders->result();
        
        $this->db->select("
            A.product_price,
            A.quantity,
            A.remarks,
            A.id as deal_order_id,
            B.id as deal_id,
            B.name,
            B.alias,
            B.description,
        ");
        $this->db->from('deals_order_items A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
        $this->db->where('A.transaction_id', $id);
        $deals_query = $this->db->get();
        $deals = $deals_query->result();

        $this->db->from('personnel_tb');
        $this->db->select('name,contact_number');
        $this->db->where('reference_code', $info[0]->moh);
        $this->db->where('assigned_store', $info[0]->store);
        $query_orders = $this->db->get();
        $personnel = $query_orders->result();

        $this->db->from('bank_account_tb');
        $this->db->select('*');
        $this->db->where('store_id', $info[0]->store);
        $this->db->where('indicator', $info[0]->payops);
        $query_orders = $this->db->get();
        $bank = $query_orders->result();

        $join_data['clients_info'] = $info[0];
        $join_data['order_details'] = array_merge($orders, $deals);
        $join_data['personnel'] = $personnel[0];
        $join_data['bank'] = $bank[0];

        // print_r($join_data);
        return $join_data;
    }

    public function get_deal_categories() {
        $this->db->select("id, name");
        $this->db->from("dotcom_deals_category");
        return $this->db->get()->result();
    }
    
    public function get_caters_package_categories() {
        $this->db->select("id, category_name as name");
        $this->db->from("catering_category_tb");
        return $this->db->get()->result();
    }

    public function getProductCategories() {
        $this->db->select("id, category_name as name");
        $this->db->from("category_tb");
        return $this->db->get()->result();
    }

    public function getPackageCategories() {
        $this->db->select("id, category_name as name");
        $this->db->from("catering_category_tb");
        return $this->db->get()->result();
    }


    function getStoreCateringProductCount($store_id, $category_id, $status, $search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('catering_products A');
        $this->db->join('products_tb B', 'B.id = A.product_id');
        $this->db->join('category_tb C', 'C.id = B.category');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->or_like('C.category_name', $search);
            $this->db->group_end();
        }

        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if(isset($category_id)) $this->db->where('C.id', $category_id);
        
        if($status)
            $this->db->where('A.status', $status);
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }
    function getStoreProductCount($store_id, $category_id, $status, $search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('region_da_log A');
        $this->db->join('products_tb B', 'B.id = A.product_id');
        $this->db->join('category_tb C', 'C.id = B.category');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->or_like('C.category_name', $search);
            $this->db->group_end();
        }

        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if(isset($category_id)) $this->db->where('C.id', $category_id);
        
        if($status)
            $this->db->where('A.status', $status);
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    
    function getStoreCatersPackageCount($store_id, $category_id, $status, $search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('catering_region_da_log A');
        $this->db->join('catering_packages_tb B', 'B.id = A.product_id');
        $this->db->join('catering_category_tb C', 'C.id = B.category');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->or_like('C.category_name', $search);
            $this->db->group_end();
        }

        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if(isset($category_id)) $this->db->where('C.id', $category_id);
        
        if($status)
            $this->db->where('A.status', $status);
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function getStoreCatersPackageAddonsCount($store_id, $status, $search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('catering_package_addons_tb	 A');
        $this->db->join('catering_packages_tb B', 'B.id = A.product_id');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->group_end();
        }

        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if(isset($category_id)) $this->db->where('C.id', $category_id);
        
        if($status)
            $this->db->where('A.status', $status);
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }
    
    
    function getStoreCatersProductAddonsCount($store_id, $status, $search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('catering_product_addons_tb	 A');
        $this->db->join('products_tb B', 'B.id = A.product_id');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->group_end();
        }

        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if(isset($category_id)) $this->db->where('C.id', $category_id);
        
        if($status)
            $this->db->where('A.status', $status);
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function getStoreCatersProductAddons($row_no, $row_per_page, $store_id,  $status, $order_by, $order, $search) {
        $this->db->select('A.id, B.name, A.store_id, B.add_details, B.product_image');

        $this->db->from('catering_product_addons_tb	 A');
        $this->db->join('products_tb B', 'B.id = A.product_id');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->group_end();
        }
            
        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }
    
    function getStoreCatersPackageAddons($row_no, $row_per_page, $store_id,  $status, $order_by, $order, $search) {
        $this->db->select('A.id, B.name, A.store_id, B.add_details, B.product_image');
        $this->db->from('catering_package_addons_tb A');
        $this->db->join('catering_packages_tb B', 'B.id = A.product_id');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->group_end();
        }
            
        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }

    function getStoreCatersPackages($row_no, $row_per_page, $store_id, $category_id,  $status, $order_by, $order, $search) {
        $this->db->select('A.id, B.name, A.store_id, B.product_image, B.add_details, C.category_name');
        $this->db->from('catering_region_da_log A');
        $this->db->join('catering_packages_tb B', 'B.id = A.product_id');
        $this->db->join('catering_category_tb C', 'C.id = B.category');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->or_like('C.category_name', $search);
            $this->db->group_end();
        }
            
        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if(isset($category_id)) $this->db->where('C.id', $category_id);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }

    function getStoreCateringProducts($row_no, $row_per_page, $store_id, $category_id,  $status, $order_by, $order, $search) {
        $this->db->select('A.id, B.name, A.store_id, B.add_details, B.product_image, C.category_name');
        $this->db->from('catering_products A');
        $this->db->join('products_tb B', 'B.id = A.product_id');
        $this->db->join('category_tb C', 'C.id = B.category');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->or_like('C.category_name', $search);
            $this->db->group_end();
        }
            
        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if(isset($category_id)) $this->db->where('C.id', $category_id);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }

    function getStoreProducts($row_no, $row_per_page, $store_id, $category_id,  $status, $order_by, $order, $search) {
        $this->db->select('A.id, B.name, B.product_image, B.price, A.store_id, B.add_details, C.category_name');
        $this->db->from('region_da_log A');
        $this->db->join('products_tb B', 'B.id = A.product_id');
        $this->db->join('category_tb C', 'C.id = B.category');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->or_like('C.category_name', $search);
            $this->db->group_end();
        }
            
        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        $this->db->where('A.status', $status);

        if(isset($category_id)) $this->db->where('C.id', $category_id);

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }

    function getStoreDealsCount($store_id, $category_id, $status, $search) {
        $this->db->select('count(*) as all_count');

        $this->db->from('deals_region_da_log A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->or_like('B.alias', $search);
            $this->db->group_end();
        }

        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        
        if(isset($category_id)) $this->db->where('A.platform_category_id', $category_id);
        
        $this->db->where('A.status', $status);

        $this->db->group_start();
        $this->db->where('B.available_start_datetime <=',date('Y-m-d H:i:s'));
        $this->db->where('B.available_end_datetime >=',date('Y-m-d H:i:s'));
        $this->db->or_where('B.available_start_datetime',null);
        $this->db->or_where('B.available_end_datetime', null);
        $this->db->group_end();
        
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    function updateStoreDeal($id, $status){
		$this->db->set('status', $status);
        $this->db->where("id", $id);
        $this->db->update("deals_region_da_log");
    }
    
    function updateStoreCatersPackage($id, $status){
		$this->db->set('status', $status);
        $this->db->where("id", $id);
        $this->db->update("catering_region_da_log");
    }
    
    function updateStoreCatersProductAddon($id, $status){
		$this->db->set('status', $status);
        $this->db->where("id", $id);
        $this->db->update("catering_product_addons_tb");
    }

    function updateStoreCatersPackageAddon($id, $status){
		$this->db->set('status', $status);
        $this->db->where("id", $id);
        $this->db->update("catering_package_addons_tb");
    }

    function updateStoreProduct($id, $status){
		$this->db->set('status', $status);
        $this->db->where("id", $id);
        $this->db->update("region_da_log");
    }
    function updateStoreCateringProduct($id, $status){
		$this->db->set('status', $status);
        $this->db->where("id", $id);
        $this->db->update("catering_products");
    }

    function getStoreDeals($row_no, $row_per_page, $store_id, $category_id, $status, $order_by, $order, $search) {
        $this->db->select('A.id, B.alias, B.name, B.product_image, A.store_id');
        $this->db->from('deals_region_da_log A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');

        if($search){
            $this->db->group_start();
            $this->db->like('B.name', $search);
            $this->db->or_like('B.alias', $search);
            $this->db->group_end();
        }
            
        $this->db->where('B.status', 1);
        $this->db->where('A.store_id', $store_id);
        

        if(isset($category_id)) $this->db->where('A.platform_category_id', $category_id);
        
        $this->db->where('A.status', $status);

        $this->db->group_start();
        $this->db->where('B.available_start_datetime <=',date('Y-m-d H:i:s'));
        $this->db->where('B.available_end_datetime >=',date('Y-m-d H:i:s'));
        $this->db->or_where('B.available_start_datetime',null);
        $this->db->or_where('B.available_end_datetime', null);
        $this->db->group_end();

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);
        
        return $this->db->get()->result();
    }

    
    function get_fname_lname_email($id){
        $this->db->select('first_name,last_name,email');
        $this->db->from('fb_users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row(); 
    }

    function get_fname_lname_email_mobile($id){
        $this->db->select('first_name,last_name,email');
        $this->db->from('mobile_users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row(); 
    }

    function updateStoreOrStatusCateringTransaction($request, $user_id, $password,$transaction_id,$to_store_id,$to_status_id){
        $this->db->select("password");
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $row = $query->row();

            if (password_verify($password, $row->password)) {

                if ($request == 'change_status') {
                    
                    $this->db->set('status',$to_status_id);

                    switch($to_status_id){
                        case 1:
                            $this->db->set('uploaded_contract','');
                            $this->db->set('initial_payment_proof','');
                            $this->db->set('final_payment_proof','');
                            break;
                        case 4:
                            $this->db->set('initial_payment_proof','');
                            $this->db->set('final_payment_proof','');
                            break;
                        case 6:
                            $this->db->set('final_payment_proof','');
                            break;
                    }
                    $this->db->where('id', $transaction_id);
                    $this->db->update('catering_transaction_tb');
                    return true;
                }
                else if ($request == 'store_transfer') {
                    
                    $this->db->set('store', $to_store_id);
                    $this->db->where('id', $transaction_id);
                    $this->db->update('catering_transaction_tb');
                    return true;
                }
            }
            else {
                return "Wrong Password";
            } 
        } 
        else {
            return false;
        }
    }

    function updateStoreOrStatusSnackshopTransaction($request,$user_id,$password,$transaction_id,$to_store_id,$to_status_id){
        $this->db->select("password");
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $row = $query->row();

            if (password_verify($password, $row->password)) {

                if ($request == 'change_status') {
                    
                    $this->db->set('status',$to_status_id);
                    if ($to_status_id == 1) {
                        $this->db->set('payment_proof','');
                    }
                    $this->db->where('id', $transaction_id);
                    $this->db->update('transaction_tb');
                    return true;
                }
                else if ($request == 'store_transfer') {
                    
                    $this->db->set('store', $to_store_id);
                    $this->db->where('id', $transaction_id);
                    $this->db->update('transaction_tb');
                    return true;
                }
            }
            else {
                return "Wrong Password";
            } 
        } 
        else {
            return false;
        }
    }

    function generate_shop_invoice_num($transaction_id){
        $curr_year = date("yy");
        $data = array(
            'year' => $curr_year,
            'dateadded'         => date('Y-m-d H:i:s'),
            'transaction_id'    => $transaction_id
        );
        $this->db->insert('invoice_tb', $data);
        $insert_id = $this->db->insert_id();
        $return_data['id'] = $insert_id;
        $return_data['status'] = ($this->db->affected_rows()) ? TRUE : FALSE;
        if($return_data['status'] == TRUE){
            $gen = '%06d';
            $inv = sprintf($gen, $insert_id);
            $invoice_num = date("y").'-'.$inv;
            $this->db->set('invoice_num', $invoice_num);
            $this->db->where('id', $transaction_id);
            $this->db->update('transaction_tb');
            return ($this->db->affected_rows()) ? 1 : 0;
        }
    }

    function update_shop_on_click($transaction_id,$trans_action){

        $this->db->set('on_click',$trans_action);
        $this->db->where('id', $transaction_id);
        $this->db->update('transaction_tb');

        return $this->db->affected_rows() ? 1 : 0;
    }

    function generate_catering_invoice_num($id){
        $curr_year = date("yy");
        $data = array(
            'year' => $curr_year,
            'dateadded'         => date('Y-m-d H:i:s'),
            'transaction_id'    => $id
        );
        $this->db->insert('catering_invoice_tb', $data);
        $insert_id = $this->db->insert_id();
        $return_data['id'] = $insert_id;
        $return_data['status'] = ($this->db->affected_rows()) ? TRUE : FALSE;
        if($return_data['status'] == TRUE){
            $gen = '%06d';
            $inv = sprintf($gen, $insert_id);
            $invoice_num = date("y").'-'.$inv;
            $this->db->set('invoice_num', $invoice_num);
            $this->db->where('id', $id);
            $this->db->update('catering_transaction_tb');
            return ($this->db->affected_rows()) ? 1 : 0;
        }
    }

    function update_catering_on_click($id,$trans_action){

        $this->db->set('on_click',$trans_action);
        $this->db->where('id', $id);
        $this->db->update('catering_transaction_tb');

        return $this->db->affected_rows() ? 1 : 0;
        // return $form_data;  
    }

    function update_catering_status($id,$action){           
        $this->db->set('status', $action);
        $this->db->where('id', $id);
        $this->db->update('catering_transaction_tb');
        return ($this->db->affected_rows()) ? 1 : 0;
    }

    function update_shop_status($transaction_id,$status){   
        if ($status == 3) {
            $raffle_code = "RC".substr(md5(uniqid(mt_rand(), true)), 0, 6);
            $this->db->set('application_status',1);
            $this->db->set('generated_raffle_code',$raffle_code);
            $this->db->where('trans_id', $transaction_id);
            $this->db->update('raffle_ss_registration_tb');
        }

        if ($status == 6) {
            $this->db->select('*');
            $this->db->from('giftcard_users');
            $this->db->where('trans_id',$transaction_id);
            $result = $this->db->get()->result();

            if (!empty($result)) {
                foreach ($result as $key => $res) {
                    $giftcard_number = "GC".substr(md5(uniqid(mt_rand(), true)), 0, 6);
                    $this->db->set('status',1);
                    $this->db->set('giftcard_number',$giftcard_number);
                    $this->db->where('trans_id', $res->trans_id);
                    $this->db->where('id', $res->id);
                    $this->db->update('giftcard_users');
                }
            }
        }
        
        $this->db->set('status', $status);
        $this->db->where('id', $transaction_id);
        $this->db->update('transaction_tb');
        return ($this->db->affected_rows()) ? 1 : 0;

    }
    
    function validate_partner_company_employee_id_number($redeem_id, $id_number){   
        $this->db->set('partner_company_id_number', $id_number);
        $this->db->where('id', $redeem_id);
        $this->db->update('deals_redeems_tb');
        return ($this->db->affected_rows()) ? 1 : 0;
    }

    function validate_ref_num($transaction_id, $ref_num){   
        $this->db->select('id');
        $this->db->from('transaction_tb');
        $this->db->where('reference_num', $ref_num);
        $query = $this->db->get();

        if($query->num_rows() > 0)
        {   
            return "Invalid Reference number";
        }
        else{
            $this->db->set('reference_num', $ref_num);
            $this->db->where('id', $transaction_id);
            $this->db->update('transaction_tb');
            return ($this->db->affected_rows()) ? 1 : 0;
        }   
    }

    function uploadPayment($id,$data,$file_name){
        $file_name = $data['file_name'];
        $this->db->set('payment_proof', $file_name);
        $this->db->set('status', 2);
        $this->db->where("id", $id);
        $this->db->update("transaction_tb");
        return ($this->db->affected_rows()) ? 1 : 0;
    }

    public function getGroups(){
        $this->db->select("
            id,
            name,
            description,
        ");

        $this->db->from('groups');
        
        $query = $this->db->get();
        return $query->result();

    }

    public function getUser($user_id){
        $this->db->select('
            A.id,
            A.first_name,
            A.last_name,
            A.phone,
            A.company,
        ');

        $this->db->from('users A');
        $this->db->where('A.id', $user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getUsersCount($search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('users A');

        if($search){
            $this->db->group_start();
            $this->db->like('A.first_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like('A.email', $search);
            $this->db->group_end();
        }
            
        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getUserGroups($user_id){
        
        $this->db->select("
            B.id,
            B.name,
            B.description,
        ");

        $this->db->from('users_groups A');
        $this->db->join('groups B', 'B.id = A.group_id');
        $this->db->where('A.user_id',$user_id);
        

        $query = $this->db->get();
        return $query->result();
    }

    public function getUsers($row_no, $row_per_page, $order_by,  $order, $search){
        $this->db->select("
            A.id,
            A.active,
            A.first_name,
            A.last_name,
            A.email
        ");

        $this->db->from('users A');

        if($search){
            $this->db->group_start();
            $this->db->like('A.first_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like('A.email', $search);
            $this->db->group_end();
        }
            
        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();

        return $query->result();
    }

    public function changeStatusUserDiscount($discount_users_id, $status){
		$this->db->set('status', (int) $status);
        $this->db->where('id', $discount_users_id);
        $this->db->update("discount_users");
    }

    public function completeRedeem($redeem_id, $today){
		$this->db->set('status', 6);
		$this->db->set('completed_date', $today);
        $this->db->where('id', $redeem_id);
        $this->db->update("deals_redeems_tb");
    }

    
    public function declineRedeem($redeem_id, $today){
		$this->db->set('status', 4);
		$this->db->set('declined_date', $today);
        $this->db->where('id', $redeem_id);
        $this->db->update("deals_redeems_tb");
    }
    
    public function getPopclubRedeemItems($redeem_id){
        $this->db->select("
            A.price,
            A.quantity,
            A.remarks,
            B.alias,
            B.description,
            B.is_partner_company,
        ");
        $this->db->from('deals_order_items A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
        $this->db->where('A.redeems_id', $redeem_id);

        $query = $this->db->get();
        return $query->result();
    }

    public function getPopclubRedeem($redeem_code){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.redeem_code,
            A.expiration,
            A.purchase_amount,
            A.invoice_num,
            A.partner_company_id_number,
            B.add_name as client_name,
            B.fb_user_id,
            B.mobile_user_id,
            B.payops,
            B.contact_number,
            B.email,
            B.address,
            B.add_address,
            C.name as store_name
        ");
        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        $this->db->where('A.redeem_code', $redeem_code);

        $query = $this->db->get();
        return $query->row();
    }

    public function getDiscount($discount_user_id){
        $this->db->select("
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.dateadded,
            A.id_front,
            A.id_back,
            A.discount_id,
            A.status,
            B.name as discount_name,

            C.first_name as fb_first_name,
            C.last_name as fb_last_name,

            D.first_name as mobile_first_name,
            D.last_name as mobile_last_name,
        ");
        $this->db->from('discount_users A');
        $this->db->join('discount B', 'B.id = A.discount_id');
        $this->db->join('fb_users C', 'C.id = A.fb_user_id','left');
        $this->db->join('mobile_users D', 'D.id = A.mobile_user_id','left');

        $this->db->where('A.id', $discount_user_id);

        $query = $this->db->get();
        return $query->row();
    }

    public function getDiscountsCount($status, $search){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('discount_users A');
        $this->db->join('discount B', 'B.id = A.discount_id');

        if($status)
            $this->db->where('A.status', $status);

            
        if($search){
            $this->db->group_start();
            $this->db->like('A.id_number', $search);
            $this->db->or_like('A.first_name', $search);
            $this->db->or_like('A.middle_name', $search);
            $this->db->or_like('A.last_name', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }
    
    public function getPopclubRedeemsCount($status, $search, $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        $this->db->where('A.platform_id', 1);

        if($status)
            $this->db->where('A.status', $status);

        if(!empty($store))
            $this->db->where_in('A.store', $store);
            
        if($search){
            $this->db->group_start();
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getDiscounts($row_no, $row_per_page, $status, $order_by,  $order, $search){
        
        $this->db->select("
            A.id,
            A.first_name,
            A.middle_name,
            A.last_name,
            A.birthday,
            A.id_number,
            A.dateadded,
            A.discount_id,
            A.status,
            B.name as discount_name,
            B.percentage,
        ");
        $this->db->from('discount_users A');
        $this->db->join('discount B', 'B.id = A.discount_id');
        
            
        if($status)
            $this->db->where('A.status', $status);


            if($search){
                $this->db->group_start();
                $this->db->like('A.id_number', $search);
                $this->db->or_like('A.first_name', $search);
                $this->db->or_like('A.middle_name', $search);
                $this->db->or_like('A.last_name', $search);
                $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
                $this->db->group_end();
            }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getPopclubRedeems($row_no, $row_per_page, $status, $order_by,  $order, $search, $store){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.redeem_code,
            A.expiration,
            A.purchase_amount,
            B.mobile_user_id,
            B.fb_user_id,
            B.add_name as client_name,
            B.payops,
            C.name as store_name
        ");
        $this->db->from('deals_redeems_tb A');
        $this->db->join('deals_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        $this->db->where('A.platform_id', 1);
        
            
        if($status)
            $this->db->where('A.status', $status);

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        if($search){
            $this->db->group_start();
            $this->db->like('A.redeem_code', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

    public function getSnackshopOrderItemsByTransactionId($transaction_id){
        $this->db->select("
            A.id as order_item_id,
            A.price,
            A.product_price,
            A.quantity,
            A.remarks,
            A.product_label,
            A.deal_id,
            A.deal_discount_percentage,
            B.id as product_id,
            B.name,
            B.description,
            B.add_details,
            C.promo_discount_percentage,
        ");
        $this->db->from('order_items A');
        $this->db->join('products_tb B', 'B.id = A.product_id');
        $this->db->join('dotcom_deals_tb C', 'C.id = A.deal_id', 'left');
        $this->db->where('A.transaction_id', $transaction_id);
        $products_query = $this->db->get();
        $products = $products_query->result();

		return $products;
    }

    public function getDealOrderItemsByTransactionId($transaction_id){
        $this->db->select("
            A.id as deal_order_item_id,
            A.price,
            A.product_price,
            A.quantity,
            A.remarks,
            B.id as deal_id,
            B.name as deal_name,
            B.alias,
            B.description,
        ");
        $this->db->from('deals_order_items A');
        $this->db->join('dotcom_deals_tb B', 'B.id = A.deal_id');
        $this->db->where('A.transaction_id', $transaction_id);
        $deals_query = $this->db->get();
        $deals = $deals_query->result();
        
		return $deals;
    }

    public function getSnackshopOrder($tracking_no){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.invoice_num,
            A.hash_key,

            A.discount,
            A.discount_user_id,
            A.reseller_discount,
            A.giftcard_discount,
            A.distance_price,
            A.cod_fee,

            A.payment_proof,
            A.reference_num,
            A.store,

            B.add_name as client_name,
            B.payops,
            B.contact_number,
            B.email,
            B.address,
            B.add_address,
            B.fb_user_id,
            B.mobile_user_id,
            
            C.name as store_name,
            E.name AS discount_name,
            E.percentage AS discount_percentage,
        ");
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        $this->db->join('discount_users D', 'D.id = A.discount_user_id','left');
        $this->db->join('discount E', 'E.id = D.discount_id','left');
        $this->db->where('A.tracking_no', $tracking_no);

        $query = $this->db->get();
        return $query->row();
    }

    public function getCateringBookingItems($transaction_id){
        $this->db->select("
            A.id,
            A.product_price,
            A.quantity,
            A.remarks,
            A.product_label,
            A.type,
            B.id as product_id,
            B.name,
            B.description,
            B.add_details,
        ");
        $this->db->from('catering_order_items A');
        $this->db->join('catering_packages_tb B', 'B.id = A.product_id');
        $this->db->where('A.transaction_id', $transaction_id);
        $this->db->where('A.type', 'main');
        $query_catering_packages = $this->db->get();
        $catering_packages = $query_catering_packages->result();

        $this->db->select("
            B.id,
            B.product_price,
            B.quantity,
            B.remarks,
            B.product_label,
            B.type,
            A.id as product_id,
            A.name,
            A.description,
            A.add_details,
        "); 
        $this->db->from('products_tb A');
        $this->db->join('catering_order_items B', 'B.product_id = A.id' ,'left');
        $this->db->where('B.transaction_id', $transaction_id);
        $this->db->where('B.type', 'product');
        $query_catering_products= $this->db->get();
        $catering_products = $query_catering_products->result();

        $this->db->select("
            B.product_price,
            B.quantity,
            B.remarks,
            B.product_label,
            B.type,
            A.name,
            A.description,
            A.add_details,
        "); 
        $this->db->from('products_tb A');
        $this->db->join('catering_order_items B', 'B.product_id = A.id' ,'left');
        $this->db->where('B.transaction_id', $transaction_id);
        $this->db->where('B.type', 'addon');
        $query_catering_add_ons = $this->db->get();
        $catering_add_ons = $query_catering_add_ons->result();

        return array_merge($catering_packages, $catering_products, $catering_add_ons);
    }

    public function getCateringBooking($tracking_no){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.serving_time,
            A.tracking_no,
            A.invoice_num,
            A.hash_key,
            A.logon_type,
            A.serving_time,
            A.start_datetime,
            A.end_datetime,
            A.message,
            A.event_class,
            A.company_name,
            A.remarks,

            A.purchase_amount,
            A.service_fee,
            A.night_diff_fee,
            A.additional_hour_charge,
            A.cod_fee,
            A.distance_price,

            A.payment_plan,

            A.uploaded_contract,
            
            A.initial_payment,
            A.initial_payment_proof,

            A.final_payment,
            A.final_payment_proof,
            
            A.reference_num,
            A.store,
            A.discount,
            A.discount_user_id,
            B.fb_user_id,
            B.mobile_user_id,

            B.add_name as client_name,
            B.fname,
            B.lname,
            B.add_address,
            B.payops,
            B.email,
            B.add_contact,
            B.contact_number,

            C.name as store_name,
            C.address as store_address,
            C.contact_person as store_person,
            C.contact_number as store_contact,
            C.email as store_email,

            E.name AS discount_name,
            E.percentage AS discount_percentage,
        ");
        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        $this->db->join('discount_users D', 'D.id = A.discount_user_id','left');
        $this->db->join('discount E', 'E.id = D.discount_id','left');
        $this->db->where('A.tracking_no', $tracking_no);

        $query = $this->db->get();
        return $query->row();
    }



    public function getCateringBookingsCount($status, $search, $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        
            
        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }
        
        if($status)
            $this->db->where('A.status', $status);

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getCateringBookings($row_no, $row_per_page, $status, $order_by,  $order, $search, $store){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.serving_time,
            A.tracking_no,
            A.invoice_num,

            A.purchase_amount,
            A.service_fee,
            A.night_diff_fee,
            A.additional_hour_charge,
            A.cod_fee,
            A.distance_price,
            
            A.reference_num,
            A.store,
            A.discount,

            B.add_name as client_name,
            B.payops,
            C.name as store_name
        ");

        $this->db->from('catering_transaction_tb A');
        $this->db->join('catering_client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        
        if($status)
            $this->db->where('A.status', $status);
            
        if(!empty($store))
            $this->db->where_in('A.store', $store);

        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like('B.add_name', $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getSnackshopOrders($row_no, $row_per_page, $status, $order_by,  $order, $search, $store){
        $this->db->select("
            A.id,
            A.status,
            A.dateadded,
            A.tracking_no,
            A.purchase_amount,
            A.distance_price,
            A.invoice_num,

            A.discount,
            A.reseller_discount,
            A.giftcard_discount,
            A.distance_price,
            A.cod_fee,
            
            A.payment_proof,
            A.reference_num,
            A.store,

            B.payops,
            B.add_name as client_name,
            B.add_address,
            C.name as store_name
        ");
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');
        

        if($status)
            $this->db->where('A.status', $status);
            
        if(!empty($store))
            $this->db->where_in('A.store', $store);

        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like("B.add_name", $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $this->db->limit($row_per_page, $row_no);
        $this->db->order_by($order_by, $order);

        $query = $this->db->get();
        return $query->result();
    }

    public function getSnackshopOrdersCount($status, $search, $store){
        $this->db->select('count(*) as all_count');
            
        $this->db->from('transaction_tb A');
        $this->db->join('client_tb B', 'B.id = A.client_id');
        $this->db->join('store_tb C', 'C.store_id = A.store');

        if($status)
            $this->db->where('A.status', $status);

        if(!empty($store))
            $this->db->where_in('A.store', $store);

        if($search){
            $this->db->group_start();
            $this->db->like('A.tracking_no', $search);
            $this->db->or_like("B.add_name", $search);
            $this->db->or_like('C.name', $search);
            $this->db->or_like('A.purchase_amount', $search);
            $this->db->or_like('A.invoice_num', $search);
            $this->db->or_like("DATE_FORMAT(A.dateadded, '%M %e, %Y')", $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $query->row()->all_count;
    }

    public function getStoredSessionId($id){
        $this->db->select('session_id');
		$this->db->from('users');
		$this->db->where('id', $id);

		$query = $this->db->get();
		$row = $query->row();

        if ($row !== null) {
            return $row->session_id;
        } else {
            return ""; 
        }
    }

    public function stores_by_user_id($user_id, $isAdmin){

        if($isAdmin){
            $this->db->select('store_id');
            $this->db->from('store_tb');
            $query = $this->db->get();
            $store = $query->result();

            $store_id = [];
            foreach ($store as $item) {
                $store_id[] = $item->store_id;
            }

            return $store_id;
        }

        $this->db->select('A.store_id');
        $this->db->from('store_tb A');
        $this->db->join('users_store_groups B', 'B.store_id = A.store_id', 'left');
        $this->db->join('users C', 'C.id = B.user_id', 'left');
        $this->db->where('C.id', $user_id);

        $query = $this->newteishop->get();
        $store = $query->result();


        $store_id = [];
        foreach ($store as $item) {
            $store_id[] = $item->store_id;
        }

       return $store_id;

    }

    function getStoreName($store_id){
        $this->db->select('
            name
        ');

        $this->db->from('store_tb');
        $this->db->where_in('store_id', $store_id);

        $query = $this->db->get();
        return $query->result();
    }

}