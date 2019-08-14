<?php
    /**
     * Created By ${pROJECT_NAME}.
     * User: pfinal
     * Date: 2019/8/14
     * Time: 下午4:46
     * ----------------------------------------
     *
     */

    // 标签类 用来定义 需要识别的 类

    class  Type
    {
        const GOOD = '好的';
        const BAD = '坏的';
    }

    //朴素贝叶斯算法是基于一个训练集合工作的，根据这个训练集从而做出相应的预测。

    class Classifier
    {
        private $types = [Type::GOOD, Type::BAD];
        private $words = [Type::GOOD => [], Type::BAD => []];
        private $documents = [Type::GOOD => 0, Type::BAD => 0];  // 好坏各为0

        public function guess($statement)
        {
            $words = $this->getWords($statement); // 获得单词
            $best_likelihood = 0;
            $best_type = null;
            foreach ($this->types as $type) {
                $likelihood = $this->pTotal($type); // calculate P(Type)\
                foreach ($words as $word) {
                    $likelihood *= $this->p($word, $type); // calculate P(word, Type)
                }
                if ($likelihood > $best_likelihood) {
                    $best_likelihood = $likelihood;
                    $best_type = $type;
                }
            }

            return $best_type;
        }

        public function getWords($string)
        {
            // 这里应该用中文分词
            return preg_split('/\s+/', preg_replace('/[^A-Za-z0-9\s]/', '', strtolower($string)));
        }

        public function pTotal($type)
        {
            return ($this->documents[$type] + 1) / (array_sum($this->documents) + 1);
        }

        public function p($word, $type)
        {
            $count = 0;
            if (isset($this->words[$type][$word])) {
                $count = $this->words[$type][$word];
            }

            return ($count + 1) / (array_sum($this->words[$type]) + 1);
        }

        public function learn($statement, $type)
        {
            $words = $this->getWords($statement);
            foreach ($words as $word) {
                if (!isset($this->words[$type][$word])) {
                    $this->words[$type][$word] = 0;
                }
                $this->words[$type][$word]++; // increment the word count for the type
            }
            $this->documents[$type]++; // increment the document count for the type
        }
    }

    $classifier = new Classifier();
    $classifier->learn('Symfony is the best', Type::GOOD);
    $classifier->learn('PhpStorm is great', Type::GOOD);
    $classifier->learn('Iltar complains a lot', Type::BAD);
    $classifier->learn('No Symfony is bad', Type::BAD);
    var_dump($classifier->guess('Symfony is great')); // string(8) "positive"
    var_dump($classifier->guess('I complain a lot')); // string(8) "negative"