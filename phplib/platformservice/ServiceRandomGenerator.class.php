<?php
/*
 * description:此类用于
 * 当产生的第一个随机数(0到某个正整数数值范围)不满足要求时，
 * 可以从剩下的数中再随机产生一个数，并依次类推
 */

namespace Phplib\PlatformService;

class ServiceRandomGenerator {
    var $mMaxNumber = 0;
    var $mArrayCandidates = array();
    var $mCurMaxIdx = 0;
    public function __construct($maxNumber) {
        $this->mMaxNumber = $maxNumber;
        $this->mCurMaxIdx = $maxNumber - 1;
        for ($i = 0; $i < $maxNumber; $i ++) {
            $this->mArrayCandidates[] = $i;
        }
    }

    public function GetRandom() {
        if (-1 == $this->mCurMaxIdx) {
            return -1;
        }
        $ret = rand(0, $this->mCurMaxIdx);
        $tmp = $this->mArrayCandidates[$ret];
        $this->mArrayCandidates[$ret]
            = $this->mArrayCandidates[$this->mCurMaxIdx];
        $this->mArrayCandidates[$this->mCurMaxIdx] = $tmp;
        $this->mCurMaxIdx --;
        return $tmp;
    }
}
