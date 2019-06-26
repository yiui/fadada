<?php
/**
 * 法大大合同对接接口
 * 合同处理
 * 个人企业存证
 * 合同签署
 */

namespace yiui\fadada;

use yiui\fadada\Encrypt;

/**
 * Class FddServer
 */
class FddServer
{
    /**
     * @var string
     */
    private $appId;
    /**
     * @var string
     */
    private $appSecret;
    /**
     * @var string
     */
    private $timestamp;
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $version = "2.0";

    /**
     * FddServer constructor.
     * @param string $appId
     * @param string $appSecret
     * @param string $host
     * @param array $options 其他参数后期补充
     */
    public function __construct($appId, $appSecret, $host, $options = [])
    {
        $this->timestamp = date("YmdHis");
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->host = $host;
        $this->options = $options;
    }


    /**
     * 用户或企业账号 获取客户编码
     * @param inter $open_id 用户在接入方唯一id
     * @param inter $account_type 账户类型，1个人，2企业
     * @return array
     */
    public function accountRegister($open_id, $account_type = 1)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $account_type . $open_id))
                )
            )
        );
        return $this->curlSend("account_register", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "open_id" => $open_id,
            "account_type" => $account_type,
        ]);
    }

    /**
     * 实名信息哈希存证
     * @param string $customer_id
     * @param string $transaction_id
     * @param string $preservation_name
     * @param string $file_name
     * @param string $noper_time
     * @param string $file_size
     * @param string $original_sha25
     * @param string $cert_flag
     * @param string $cert_flag 自动申请实名证书 参 数 值 为 “0”：不申 请， 参 数 值 为 “1”：自动 申请
     * @return array
     */
    public function hashDeposit($customer_id, $transaction_id, $preservation_name, $file_name, $noper_time, $file_size, $original_sha25, $cert_flag = 0)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(
                        sha1($this->appSecret . $cert_flag . $customer_id . $file_name . $file_size . $noper_time . $original_sha25 . $preservation_name . $transaction_id))
                )
            )
        );
        return $this->curlSend("hash_deposit", 'get', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//客户编号
            "preservation_name" => $preservation_name,//存证名称
            "file_name" => $file_name,//文件名
            "noper_time" => $noper_time,//文件最后修改时间 文件最后修改时间(unix 时间，单位s):file.lastModified()/1000
            "file_size" => $file_size,//文件大小  字符类型；值单位（byte） ;最大值:“9223372036854775807” >> 2^63-1 最小值:0sha256
            "original_sha25" => $original_sha25,//文件哈希值 文件 hash 值： sha256 算法
            "transaction_id" => $transaction_id,//交易号  自定义
            "cert_flag" => $cert_flag,//是否认证成 功后自动申请实名证书 参 数 值 为 “0”：不申 请， 参 数 值 为 “1”：自动 申请
        ]);
    }


    /**
     *
     *
     * 个人实名信息存证
     * @param string $customer_id
     * @param string $name
     * @param string $idcard
     * @param string $mobile
     * @param string $preservation_name
     * @param string $preservation_data_provider
     * @param string $mobile_essential_factor
     * @param string $preservation_name
     * @param string $document_type
     * @param string $cert_flag
     * @param string $verified_type
     * @return array
     */
    public function personDeposit($customer_id, $name, $idcard, $mobile, $preservation_name, $preservation_data_provider, $mobile_essential_factor, $document_type = 0, $cert_flag = 1, $verified_type = 2)
    {
        //verifiedType=2 公安部三要素
//        $mobile_essential_factor = json_encode([
//            'transactionId' => $transactionId,//交易号
//        ]);
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1(
                        $this->appSecret . $cert_flag . $customer_id . $document_type . $idcard . $mobile . $mobile_essential_factor . $name . $preservation_data_provider . $preservation_name . $verified_type))
                )
            )
        );
        return $this->curlSend("person_deposit", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//客户编号
            "preservation_name" => $preservation_name,//存证名称
            "preservation_data_provider" => $preservation_data_provider,//存证提供方
            "verified_type" => $verified_type,//1:公安部二要素(姓名+身份证);2:手机三要素(姓名+身份证+手机号);3:银行卡三要素(姓名+身份证+银行卡);4:四要素(姓名+身份证+手机号+银行卡)Z：其他
            "name" => $name,//姓名
            "idcard" => $idcard,//证件号
            "mobile" => $mobile,//手机号
            "document_type" => $document_type,//证件类型 默认是 0：身份证， 具体查看 5.18 附录
            "mobile_essential_factor" => $mobile_essential_factor,// 手机三要素
            "cert_flag" => $cert_flag,//是 否认 证成 功后 自动 申请 实名证书参 数值 为“0”：不申请，参 数值 为“1”：自动申请
        ]);
    }


    /**
     *
     *
     * 三要素身份验证
     * @param string $name
     * @param string $idcard
     * @param string $mobile
     * @return array
     */
    public function threeElementVerifyMobile($name, $idcard, $mobile)
    {
        /**
         *
         * 3des(姓名|身份证号码|手机号， app_secret)
         **/
        $verify_element =$this->encrypt($name."|".$idcard."|". $mobile,$this->appSecret);
        $verify_element=strtoupper($verify_element[1]);
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $verify_element))
                )
            )
        );
        return $this->curlSend("three_element_verify_mobile", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "verify_element" => $verify_element,//三要素（姓名、 身份证号码、手机号码
        ]);
    }

    /**
     *
     * 3des加密
     * @param string $data
     * @param string $key
     * @return array
     */
    private function encrypt($data,$key)
    {
        try {
            if (!in_array('des-ede3', openssl_get_cipher_methods())) {
                throw new \Exception('未知加密方法');
            }
            $ivLen  = openssl_cipher_iv_length('des-ede3');
            $iv     = openssl_random_pseudo_bytes($ivLen);
            $result = bin2hex(openssl_encrypt($data, 'des-ede3', $key, OPENSSL_RAW_DATA, $iv));
            if (!$result) {
                throw new \Exception('加密失败');
            }
            return [TRUE, $result];
        } catch (\Exception $e) {
            return [FALSE, $e->getMessage()];
        }
    }
    /**
     *
     * 查询个人实名认证信息
     * @param inter $verified_serialno
     * @return array
     */
    public function findPersonCertInfo($verified_serialno)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1(
                        $this->appSecret . $this->ascllSort([$verified_serialno])))
                )
            )
        );
        return $this->curlSend("find_personCertInfo", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "verified_serialno" => $verified_serialno,//交易号，获取认证地址时返回

        ]);
    }

    /**
     *
     * 对企业信息实名存证
     * @param string $transaction_id
     * @param string $company_customer_id
     * @param string $company_preservation_name
     * @param string $company_preservation_data_provider
     * @param string $company_name
     * @param string $credit_code
     * @param string $credit_code_file
     * @param string $company_principal_verifie_msg 企业负责人信息
     * @param string $applyNum
     * @param string $document_type
     * @param string $verified_mode
     * @param string $company_principal_type
     * @return array
     */
    public function companyDeposit($transaction_id, $company_customer_id, $company_preservation_name, $company_preservation_data_provider, $company_name, $credit_code, $credit_code_file, $company_principal_verifie_msg, $applyNum, $power_attorney_file, $document_type = 1, $verified_mode = 1, $company_principal_type = 1)
    {
        //企业负责人信息
//        $company_principal_verifie_msg = json_encode([
//            'customer_id' => $customer_id,//企业负责人客户编号
//            'preservation_name' => $preservation_name,//存证名称
//            'preservation_data_provider' => $preservation_data_provider,//存证数据提供方
//            'name' => $name,//企业负责人姓名
//            'idcard' => $idcard,//企业负责人idcard
//            'verified_type' => $verified_type,//企业负责人实名存证类型 1:公安部二要素(姓名+身份证);2:手机三要素(姓名+身份证+手机号);3:银行卡三要素(姓名+身份证+银行卡);4:四要素(姓名+身份证+手机号+ 银行卡)Z：其他
//            'customer_id' => $customer_id,//企业负责人客户编号
//        ]);
        //verifiedType=1 公安部二要素
        $public_security_essential_factor = json_encode([
            'applyNum' => $applyNum,//申请编号
        ]);
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $company_name . $company_principal_type . $company_principal_verifie_msg . $credit_code
                        . $company_customer_id . $document_type . $company_preservation_data_provider . $company_preservation_name . $transaction_id . $verified_mode))
                )
            )
        );
        return $this->curlSend("company_deposit", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $company_customer_id,//企业客户编号
            "preservation_name " => $company_preservation_name,//企业存证名称
            "preservation_data_provider" => $company_preservation_data_provider,//存证提供者
            "company_name" => $company_name,//企业名称
            "document_type" => $document_type,//证件类型 1:三证合一 2：旧版营业执照
            "credit_code" => $credit_code,//统 一社 会信用代码
            "credit_code_file" => $credit_code_file,//统 一社 会信 用代 码电子版
            "verified_mode" => $verified_mode,//实 名认 证方式1:授权委托书 2:银行对公打款
            "power_attorney_file" => $power_attorney_file,//授 权委 托书电子版
            "company_principal_type" => $company_principal_type,//企 业负 责人身份 :1.法人， 2 代理人
            "company_principal_verifie_msg" => $company_principal_verifie_msg,//json 企 业负 责人 实名 存证 信息
            "transaction_id" => $transaction_id,//交易号
            'public_security_essential_factor' => $public_security_essential_factor,
            'power_attorney_file' => $power_attorney_file,//授 权委 托书电子版
        ]);
    }

    /**
     *
     *
     * 查询企业实名认证信息
     * @param inter $verified_serialno
     * @return array
     */
    public function findCompanyCertInfo($verified_serialno)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1(
                        $this->appSecret . $this->ascllSort([$verified_serialno])))
                )
            )
        );
        return $this->curlSend("find_companyCertInfo", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "verified_serialno" => $verified_serialno,//交易号，获取认证地址时返回

        ]);
    }

    /**
     *
     * 编号证书申请
     * @param string $customer_id
     * @param string $evidence_no
     * @return array
     */
    public function applyClientNumcert($customer_id, $evidence_no)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $customer_id . $evidence_no))
                )
            )
        );
        return $this->curlSend("apply_client_numcert", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//注册账号时返回
            "evidence_no " => $evidence_no,//实名信息存证时返回
        ]);
    }


    /**
     *
     * 印章上传
     * 新增用户签章图片
     * @param string $customer_id
     * @param string $signature_img_base64
     * @return array
     */
    public function addSignature($customer_id, $signature_img_base64)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $this->ascllSort([$customer_id, $signature_img_base64])))
                )
            )
        );
        return $this->curlSend("add_signature", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//客户编号
            "signature_img_base64" => $signature_img_base64,//签章图片 base64
        ]);
    }

    /**
     *
     * 新增用户签章图片
     * @param string $customer_id
     * @param string $signature_img_base64
     * @return array
     */
    public function customSignature($customer_id, $content)
    {

        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $this->ascllSort([$content, $customer_id])))
                )
            )
        );
        return $this->curlSend("custom_signature", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//客户编号
            "content" => $content,//印章展示的内容
        ]);
    }


    /**
     *
     * 合同上传
     * @param string $contract_id
     * @param string $doc_title
     * @param string $file
     * @param string $doc_url
     * @param string $doc_type
     * @return array
     */
    public function uploaddocs($contract_id, $doc_title, $file, $doc_url, $doc_type = '.pdf')
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $this->ascllSort([$contract_id])))
                )
            )
        );
        return $this->curlSend("uploaddocs", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "contract_id" => $contract_id,//合同编号
            "doc_title" => $doc_title,//合同标题
            "doc_url " => $doc_url,//文档地址  File 文件 doc_url和 file 两个参数必选一
            "file" => $file,//PDF 文档  File 文件 doc_url和 file 两个参数必选一
            "doc_type" => $doc_type,//文档类型  .pdf
        ]);
    }


    /**
     *
     *  模板上传
     * @param string $contract_id
     * @param string $file
     * @param string $doc_url
     * @param string $doc_type
     * @return array
     */
    public function uploadtemplate($template_id, $file, $doc_url, $doc_type = '.pdf')
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $this->ascllSort([$template_id])))
                )
            )
        );
        return $this->curlSend("uploadtemplate", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "template_id" => $template_id,//模板编号
            "doc_url" => $doc_url,//文档地址 字段类型：字符串， 须为 URLdoc_url 和 file两个参数必选一
            "file" => $file,//PDF 文档  File 文件 doc_url和 file 两个参数必选一
            "doc_type" => $doc_type,//文档类型  .pdf
        ]);
    }


    /**
     *
     *  模板填充
     * @param string $doc_title
     * @param string $template_id
     * @param string $contract_id
     * @param string $font_size
     * @param string $parameter_map
     * @param string $font_type
     * @return array
     */
    public function generateContract($doc_title, $template_id, $contract_id, $font_size, $parameter_map, $font_type)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $template_id . $contract_id))
                )
            )
        );
        return $this->curlSend("generate_contract", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "doc_title" => $doc_title,//文档标题合同标题
            "template_id" => $template_id,//模板编号
            "contract_id" => $contract_id,//合同编号
            "font_size" => $font_size,//字体大小
            "parameter_map" => $parameter_map,//填充内容
            "font_type" => $font_type,//字体类型
        ]);
    }


    /**
     *
     *  自动签署
     * @param string $transaction_id
     * @param string $contract_id
     * @param string $customer_id
     * @param string $client_role
     * @param string $pagenum
     * @param string $x
     * @param string $y
     * @return array
     */
    public function extsignAuto($transaction_id, $contract_id, $customer_id, $client_role, $pagenum, $x, $y)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $customer_id))
                )
            )
        );
        return $this->curlSend("extsign_auto", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "transaction_id" => $transaction_id,//交易号
            "contract_id" => $contract_id,//合同编号
            "customer_id" => $customer_id,//客户编号
            "client_role" => $client_role,//客户角色  1-接入平台；2-仅适用互金行业担保公司或担保人；3-接入平台客户（互金行业指投资人）；4-仅适用互金行业借款企业或者借款人如果需要开通自动签权限请联系法
            "pagenum" => $pagenum,//页码 签章页码，从 0 开始。即在第一页签章，传值 0。
            "x" => $x,//盖章点 x 坐标
            "y" => $y,//盖章点 y 坐标
        ]);
    }


    /**
     *  手动签署接口
     * @param string $transaction_id
     * @param string $contract_id
     * @param string $customer_id
     * @param string $doc_title
     * @param string $return_url
     * @param string $customer_mobile
     * @return array
     */
    public function extsign($transaction_id, $contract_id, $customer_id, $doc_title, $return_url, $customer_mobile)
    {

        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $customer_id))
                )
            )
        );
        return $this->curlSend("extsign", 'get', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "transaction_id" => $transaction_id,//交易号
            "contract_id" => $contract_id,//合同编号
            "customer_id" => $customer_id,//客户编号
            "doc_title" => $doc_title,//客户角色  1-接入平台；2-仅适用互金行业担保公司或担保人；3-接入平台客户（互金行业指投资人）；4-仅适用互金行业借款企业或者借款人如果需要开通自动签权限请联系法
            "return_url" => $return_url,//页面跳转URL（签署结果同步通知）
            "customer_mobile" => $customer_mobile,//手机号
        ]);
    }


    /**
     *此接口将打开页面
     *  合同查看
     * @param string $contract_id
     * @return array
     */
    public function viewContract($contract_id)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $contract_id))
                )
            )
        );
        return $this->curlSend("view_contract", 'get', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "contract_id" => $contract_id,//合同编号

        ]);
    }


    /**
     *
     *  合同下载
     * @param string $contract_id
     * @return array
     */
    public function downLoadContract($contract_id)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $contract_id))
                )
            )
        );
        return $this->curlSend("downLoadContract", 'get', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "contract_id" => $contract_id,//合同编号

        ]);
    }


    /**
     *
     *  合同归档
     * @param string $contract_id
     * @return array
     */
    public function contractFiling($contract_id)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $this->ascllSort([$contract_id])))
                )
            )
        );
        return $this->curlSend("contractFiling", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "contract_id" => $contract_id,//合同编号

        ]);
    }


    /**
     *
     *  获取企业实名认证地址
     * @param string $customer_id
     * @param string $notify_url
     * @param string $legal_info
     * @param string $page_modify
     * @param string $company_principal_type
     * @return array
     */
    public function getCompanyVerifyUrl($customer_id, $notify_url, $legal_info, $page_modify = 1, $company_principal_type = 1)
    {

        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $company_principal_type . $customer_id . $legal_info . $notify_url . $page_modify))
                )
            )
        );
        return $this->curlSend("get_company_verify_url", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//客户编号
            "page_modify" => $page_modify,//是否允许用户页面修改1 允许，2 不允许
            "notify_url" => $notify_url,//回调地址
            "company_principal_type" => $company_principal_type,//企业负责人身份:1. 法人，2. 代理人
            "legal_info" => $legal_info,//法人信息


        ]);
    }


    /**
     *  获取个人实名认证地址
     * @param string $customer_id
     * @param string $notify_url
     * @param string $verified_way
     * @param string $page_modify
     * @return array
     */
    public function getPersonVerifyUrl($customer_id, $notify_url, $verified_way = 2, $page_modify = 1, $cert_flag = 0)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1(
                        $this->appSecret . $this->ascllSort([$cert_flag, $customer_id, $notify_url, $page_modify, $verified_way])))
                )
            )
        );
        return $this->curlSend("get_person_verify_url", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//客户编号
            "verified_way" => $verified_way,//实名认证套餐类型
            "page_modify" => $page_modify,//是否允许用户页面修改1 允许，2 不允许
            "notify_url" => $notify_url,//回调地址 异步通知认证结果
            "cert_flag" => $cert_flag,//是否认证成功后自动申请实名证书参数值为“0”：不申请，参数值为“1”：自动申请
        ]);
    }


    /**
     *
     *  实名证书申请
     * @param string $customer_id
     * @param string $verified_serialno
     * @return array
     */
    public function applyCert($customer_id, $verified_serialno)
    {
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $this->ascllSort([$customer_id, $verified_serialno])))
                )
            )
        );
        return $this->curlSend("apply_cert", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//客户编号
            "verified_serialno" => $verified_serialno,//实名认证序列号
        ]);
    }

    /**
     *
     *  编号证书申请
     * @param string $customer_id
     * @param string $verified_serialno
     * @return array
     */
    public function applyNumcert($customer_id, $verified_serialno)
    {

        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $this->ascllSort([$customer_id, $verified_serialno])))
                )
            )
        );
        return $this->curlSend("apply_numcert", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "customer_id" => $customer_id,//客户编号
            "verified_serialno" => $verified_serialno,//实名认证序列号
        ]);
    }


    /**
     *
     *  通过 uuid 下载文件
     * @param string $uuid
     * @return array
     */
    public function getFile($uuid)
    {
        $timestamp = date("YmdHis");
        $msg_digest = base64_encode(
            strtoupper(
                sha1($this->appId . strtoupper(md5($this->timestamp)) . strtoupper(sha1($this->appSecret . $this->ascllSort([$uuid])))
                )
            )
        );
        return $this->curlSend("get_file", 'post', [
            //公共参数
            "app_id" => $this->appId,
            "timestamp" => $this->timestamp,
            "v" => $this->version,
            "msg_digest" => $msg_digest,
            //业务参数
            "uuid" => $uuid,//图片 uuid 查询认证结果时返回

        ]);
    }

    /**
     *ascll码排序
     * @param array $arr
     * @return array
     */
    private function ascllSort($arr, $sf = 0)
    {
        sort($arr, $sf);
        $tmp = implode('', $arr);
        return $tmp;
    }

    /**
     *
     * @param string $url 请求路由
     * @param string $method 请求方式post/get
     * @param string $data 传入参数
     * @return array $temp 返回数组参数
     */
    private function curlSend($url, $method = 'post', $data = '')
    {
        $fadada_url = $this->host . $url . '.api';
        $ch = curl_init(); //初始化
        $headers = array('Accept-Charset: utf-8');
        //设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $fadada_url); //指定请求的URL
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method)); //提交方式
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //不验证SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //不验证SSL
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //设置HTTP头字段的数组
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)'); //头的字符串

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1); //自动设置header中的Referer:信息
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //提交数值

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //是否输出到屏幕上,true不直接输出
        $temp = curl_exec($ch); //执行并获取结果
        $temp = json_decode($temp);
        curl_close($ch);
        return $temp; //return 返回值
    }


    /**-------------------------------<end>-----------------------------------------**/
}