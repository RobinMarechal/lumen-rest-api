<?php
/**
 * Created by PhpStorm.
 * User: robin
 * Date: 12/02/18
 * Time: 21:17
 */

namespace App\Console\Commands\Api;

use App\Http\Helpers\Helper;

trait GenerateFileTemplates
{
    protected function compileControllerTemplate($namespace, $className)
    {
        $template = $this->getControllerTemplate();
        $template = str_replace('{{controller_namespace}}', $namespace, $template);
        $template = str_replace('{{controller_name}}', $className, $template);

        return $template;
    }


    protected function compileModelTemplate($namespace, $className, array $fillable, array $relations)
    {
        $template = $this->getModelTemplate();
        $template = str_replace('{{model_namespace}}', $namespace, $template);
        $template = str_replace('{{model_name}}', $className, $template);
        $template = str_replace('{{model_fillable}}', "['" . join("', '", $fillable) . "']", $template);

        foreach ($relations as $attrs) {
            $method = Helper::arrayGetOrNull($attrs, 'method');
            $relatedModel = Helper::arrayGetOrNull($attrs, 'relatedModel');
            $funcName = Helper::arrayGetOrNull($attrs, 'funcName');

            $compiledRelationFunction = $this->compileModelRelationTemplate($funcName, $relatedModel, $method);

            $template = str_replace('{{model_relation}}', $compiledRelationFunction . "\n\n{{model_relation}}", $template);
        }

        $template = str_replace("\n\n{{model_relation}}", '', $template);

        return $template;
    }


    protected function compileModelRelationTemplate($funcName, $relatedModel = null, $method = null)
    {
        $template = $this->getModelRelationTemplate();

        $template = str_replace('{{function_name}}', $funcName, $template);
        $template = str_replace('{{relation_return}}', $this->compileModelRelationReturn($relatedModel, $method), $template);

        return $template;
    }


    protected function compileModelRelationReturn($relatedModel = null, $method = null)
    {
        return $relatedModel && $method ? "return \$this->$method('App\\$relatedModel');" : '';
    }


    private function getControllerTemplate()
    {
        return
            "<?php

namespace {{controller_namespace}};

class {{controller_name}} extends ApiController
{

}";
    }


    private function getModelTemplate()
    {
        return
            '<?php

namespace {{model_namespace}};

use Illuminate\Database\Eloquent\Model;

class {{model_name}} extends Model
{
    public $timestamps = true;
    public $temporalField = \'created_at\';
    protected $fillable = {{model_fillable}};

    {{model_relation}}
}';
    }


    private function getModelRelationTemplate()
    {
        return '
    public function {{function_name}}(){
        {{relation_return}}
    }';
    }


    private function getModelRelationReturnTemplate()
    {
        return 'return $this->{{relation_method}}(\'{{related_model}}\');';
    }

}