<?php
namespace App\Nova\Helpers;

class CloudflareHelper {
    
 public static function getCloudflareEndpoint(){
     return "https://api.cloudflare.com/client/v4/zones/".env('CLOUDFLARE_ZONE_ID')."/dns_records";
 }
 
 public static function getRequiredHeader(){
     $auth_key="Authorization: Bearer ".env('CLOUDFLARE_API_KEY');
     $content_type="Content-Type: application/json";
     $arr=array($auth_key,$content_type);
     return $arr;
 }

 public static function getCentralDomain(){
     $cd=env('CENTRAL_DOMAINS');
     return $cd;
 }

 //add cname record to server
 public static function crate_cname_record($cname) {
    $ch = curl_init();
    $post_fields['type']='CNAME';
    $post_fields['name']=$cname;
    $post_fields['content']=self::getCentralDomain();
    $post_fields['proxiable']=true;
    $post_fields['proxied']=true;
    $post_fields['ttl']=1;
    $post_fields['locked']=false;
    $json_post_field=json_encode($post_fields);
    
    curl_setopt($ch, CURLOPT_URL,self::getCloudflareEndpoint());
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
                $json_post_field);
    
    curl_setopt($ch, CURLOPT_HTTPHEADER,self::getRequiredHeader());

    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    curl_close ($ch);
 }

 public static function get_dns_id_record_by_cname($cname){
    $ch = curl_init();
    $url=self::getCloudflareEndpoint().'?name='.$cname;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,self::getRequiredHeader());
    $data = curl_exec($ch);
    curl_close($ch);
    $dns_record=json_decode($data);
    if(count($dns_record->result)==1){
        return $dns_record->result[0]->id;
    }
    else{
        return null;
    }
 }

 public static function delete_dns_record_by_cname($cname){
     $id=self::get_dns_id_record_by_cname($cname);
     if($id){
        $delete_url=self::getCloudflareEndpoint().'/'.$id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$delete_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER,self::getRequiredHeader());
    
        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $server_output = curl_exec($ch);
        
        curl_close ($ch);
     }
 }

}