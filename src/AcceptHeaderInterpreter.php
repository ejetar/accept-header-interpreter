<?php
namespace Ejetar\AcceptHeaderInterpreter;

class AcceptHeaderInterpreter {
    protected $originalContent, $mime_types_collection;

    public function __construct(string $content) {
        $this->interpret($content);
    }

    public function getOriginalContent() {
        return $this->originalContent;
    }

    public function toCollection() {
        //Step 1 - Convert array to collection
        $mime_types_collection = collect($this->mime_types_collection);

        //Step 2 - Sort values by priority, based on RFC 7231, section 5.3.1 and 5.3.2
        return $mime_types_collection
            ->sortByDesc(function($media_type) {
                return count($media_type['parameters']);
            })
            ->sortByDesc(function ($media_type) {
                if ($media_type['type'] != '*' && $media_type['subtype'] != '*') {
                    return 1;
                } elseif ($media_type['type'] != '*' && $media_type['subtype'] == '*') {
                    return 0.5;
                } elseif ($media_type['type'] == '*' && $media_type['subtype'] == '*') {
                    return 0;
                }
            })
            ->sortByDesc(function ($media_type) {
                return $media_type['subtype'] = '*';
            })
            ->sortByDesc(function ($media_type) {
                return $media_type['parameters']['q'];
            });
    }

    protected function interpret(string $content) {
        $this->originalContent = $content;
        $this->mime_types_collection = [];

        //Step 1 - BEGIN: Validates the content through regular expressions
        preg_match_all(
            '/([a-z0-9\+\-\.\*]+\/[a-z0-9\+\-\.\*]+)(\;(?:[a-zA-Z]+\=[a-z\d\.]+))*(?:\,\s*)?/',
            $this->originalContent,
            $matches,
            PREG_PATTERN_ORDER
        );

        $full_match = "";
        foreach($matches[0] as $m)
            $full_match.=$m;

        if ($full_match != $this->originalContent)
            throw new \Exception("Accept header value is invalid!");
        //Step 1 - END

        //Step 2 - BEGIN
        $media_types = explode(',', trim($this->originalContent));
        foreach($media_types as $t) {
            $aux = explode(';', $t);
            $type_subtype = $aux[0];
            $type_subtype_aux = explode('/', $type_subtype);

            $type = $type_subtype_aux[0];
            $subtype = $type_subtype_aux[1];
            $parameters = [];
            for($i=1;$i<count($aux);$i++) {
                $ex = explode('=', trim($aux[$i]));
                if(
                    $ex[0] == 'q' &&
                    (!is_numeric($ex[1]) || $ex[1] < 0 || $ex[1] > 1)
                )
                    throw new \Exception("Accept header value is invalid!");

                $parameters[$ex[0]] = $ex[1];
            }

            if (!isset($parameters['q']))
                $parameters['q'] = 1;

            $this->mime_types_collection[] = compact('type','subtype','parameters');
        }
        //Step 2 - END
    }
}
