<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Attribute;
class JobFilterService
{
    protected Builder $query;
    protected string $filters;

    // Define allowed filter fields and relationships
    protected $allowedstringOperators =  ['=', '!=', 'LIKE'];
    protected $allowednumberOperators = ['=', '!=', '>', '<', '>=', '<='];
    protected $allowedbooleanOperators = ['=', '!='];
    protected $allowedenumOperators = ['=', '!=', 'IN'];
    protected $alloweddateOperators = ['=', '!=', '>', '<', '>=', '<='];
    protected $allowedRelationshipsOperators = ['=', 'HAS_ANY', 'IS_ANY', 'EXISTS'];
    
    public function __construct(Request $request)
    {
        $this->query = Job::query();

    }

    //--------------------------------------Parse Query String section---------------------------------------------


    public function parseToJson($filterString) {
        //Separte the query string by the logical Condition (AND OR)
        $tokens = preg_split('/\s+(AND|OR)\s+/', $filterString, -1, PREG_SPLIT_DELIM_CAPTURE);
    
        $parsedFilters = [];
        $currentOperator = "AND";
        $currentGroup = [];
        $nestedCondition = "";
        $depth = 0;
        foreach ($tokens as $token) {
            $token = trim($token);
    
            if ($token === "AND" || $token === "OR") {
                if ($depth === 0) {
                    if (!empty($nestedCondition)) {
                        $currentGroup[] = $this->processCondition(trim($nestedCondition));
                        $nestedCondition = "";
                    }
                    if (!empty($currentGroup)) {
                        $parsedFilters[] = [
                            "operator" => $currentOperator,
                            "conditions" => $currentGroup
                        ];
                        $currentGroup = [];
                    }
                    $currentOperator = $token;
                } else {
                    $nestedCondition .= " " . $token;
                }
                continue;
            }
    
            // Count open/close parentheses to track depth
            $openCount = substr_count($token, "(");
            $closeCount = substr_count($token, ")");
            $depth += $openCount - $closeCount;
    
            if ($depth > 0 || str_starts_with($token, "(")) {
                // Still inside a nested condition
                $nestedCondition .= " " . $token;
            } else {
                // Completed a full condition
                if (!empty($nestedCondition)) {
                    $nestedCondition .= " " . $token;
                    $currentGroup[] = $this->processCondition(trim($nestedCondition));
                    $nestedCondition = "";
                } else {
                    $currentGroup[] = $this->processCondition($token);
                }
            }
        }
    
        if (!empty($currentGroup)) {
            $parsedFilters[] = [
                "operator" => $currentOperator,
                "conditions" => $currentGroup
            ];
        }
    
        // Process conditions: PArse the nested conditon string 
        foreach ($parsedFilters as &$filter) {
            if (isset($filter['conditions'])) {
                $newConditions = [];
                foreach ($filter['conditions'] as $condition) {
                    if (is_string($condition)) {
                        // Parse the string condition
                        $parsedCondition = $this->parseConditionString($condition);
                        if (is_array($parsedCondition)) {
                            $newConditions[] = $parsedCondition;
                        } else {
                            $newConditions[] = $condition;
                        }
                    } else {
                        // Keep the existing array condition
                        $newConditions[] = $condition;
                    }
                }
                $filter['conditions'] = $newConditions;
            }
        }
    
        return $parsedFilters;
    }
    
    private function processCondition($condition) {
        // Remove wrapping parentheses
        $condition = preg_replace('/^\((.*)\)$/', '$1', $condition);
    
        //check if the operator is =
        if (preg_match('/^(\w+)=([^\s\(\)]+)$/', $condition, $matches)) {
            return [
                "key" => $matches[1],
                "operator" => "=",
                "value" => $matches[2]
            ];
          //check if the operator is Has_Any/Is_Any and explode the values into array
        } elseif (preg_match('/^(\w+)\s(HAS_ANY|IS_ANY)\s\(([^)]+)\)$/', $condition, $matches)) {
            return [
                "key" => $matches[1],
                "operator" => $matches[2],
                "value" => array_map('trim', explode(',', $matches[3]))
            ];
            //check if the filter field is attribute
        } elseif (preg_match('/^attribute:(\w+)\s*(=|!=|<=|>=|<|>|LIKE|IN)\s*(.+)$/', $condition, $matches)) {
            return [
                "key" => "attribute",
                "attribute" => $matches[1],
                "operator" => $matches[2],
                "value" =>  $matches[3]
            ];
            //check if the the operator is one of the following !=|<=|>=|<|>
        }elseif(preg_match('/^(\w+)\s*(!=|>|<|>=|<=)\s*(.+)$/', $condition, $matches)) {
            return [
                "key" => $matches[1],
                "operator" => $matches[2],
                "value" => $matches[3]
            ];

            //check if the operator is LIKE and parse the value between %
        }elseif(preg_match('/^(\w+)\s*(LIKE)\s*(.+)$/', $condition, $matches)) {
                return [
                    "key" => $matches[1],
                    "operator" => $matches[2],
                    "value" => '%'.$matches[3].'%'
                ];
            //check if the operator is IN and explode the value into array
        }elseif(preg_match('/^(\w+)\s*(IN)\s*(.+)$/', $condition, $matches)) {
            return [
                "key" => $matches[1],
                "operator" => $matches[2],
                "value" => array_map('trim', explode(',', trim($matches[3], '()'))) // Handle IN condition as an array
            ];
          //check if the operator is EXISTS and value is null   
        }elseif(preg_match('/^(\w+)\s*(EXISTS)\s*(.+)$/', $condition, $matches)) {
            return [
                "key" => $matches[1],
                "operator" => $matches[2],
                "value" => null
            ];
        }
    
        return $condition;
    }
    
    //Function used to parse the nested conditions
    private function parseConditionString($conditionString)
    {
        $tokens = preg_split('/\s+(AND|OR)\s+/', $conditionString, -1, PREG_SPLIT_DELIM_CAPTURE);
        $parsedConditions = [];
    
        for ($i = 0; $i < count($tokens); $i++) {
            $token = trim($tokens[$i]);
    
            if ($token === 'AND' || $token === 'OR') {
                $parsedConditions['logical_operator'] = $token;
                continue;
            }
    
            $parsedCondition = $this->processCondition($token);
            if (is_array($parsedCondition)) {
                $parsedConditions[] = $parsedCondition;
            } else {
                return $conditionString;
            }
        }
    
        return $parsedConditions;
    }
//--------------------------------------Apply and Validate filter Section---------------------------------------------
 
    //check if the filter is from the field list
    private function isField(string $name): bool
    {
        $fieldNames = ['title', 'description', 'company_name', 'salary_min', 'salary_max', 'is_remote', 'job_type', 'status', 'published_at', 'created_at', 'updated_at'];
        return in_array($name, $fieldNames);
    }

    //Check if the filter is a relationship
    private function isRelationship(string $name): bool
    {
        $relationshipNames = ['languages', 'locations', 'categories'];
        return in_array($name, $relationshipNames);
    }

    private function defineFilterType($name)
    {
        //if the filter name is in any of the possible field keys
        if($this->isField($name)){
            return 'field';
        //if the filter name is in any of the possible relationship keys    
        }elseif($this->isRelationship($name)){
            return 'relationship';

        //if the filter name is attribute
        }elseif($name == 'attribute'){
            return 'attribute';
        }
    }  
    

    /// Get from each field name its type
    private function fieldType($fieldName)
    {
        switch ($fieldName) {
            case 'title':
            case 'description':
            case 'company_name':
                return 'string';
            case 'salary_min':
            case 'salary_max':
                return 'number';
            case 'is_remote':
                return 'boolean';
            case 'job_type':
            case 'status':
                return 'enum';
            case 'published_at':
            case 'created_at':
            case 'updated_at':
                return 'date';
            default:
                return 'string';
        }
    }
    
    //Validate the filter field type and if it has the allowed operations
    private function fieldvalidateFilter($filter)
    {
        $fieldType = $this->fieldType($filter['key']);
        $arrayName = 'allowed'.$fieldType.'Operators';	
        if(!in_array($filter['operator'], $this->$arrayName)){
            throw new \Exception('Invalid operator for field '.$filter['key']);
        }else{
            return true;
        }
    }
    
    //Filter query if type is field
    private function fieldFilter($filter)
    {
        //validate if the field operator is valid
        $this->fieldvalidateFilter($filter);

        //filter the query depending on the operator and value 
        $this->query->where($filter['key'], $filter['operator'], $filter['value']);
    }

    private function relationvalidateFilter($filter)
    {
        if(!in_array($filter['operator'], $this->allowedRelationshipsOperators)){
            throw new \Exception('Invalid operator for field '.$filter['key']);
        }else{
            return true;
        }
    }

    //Validate the filter Relationship type and if it has the allowed operations
    private function relationshipFilter($filter)
    {
        //Validate if the relationship operator is valid
        $this->relationvalidateFilter($filter);

        //Get the field name depending on the relationship
        $fieldName =($filter['key'] == 'languages' || $filter['key'] == 'categories')?'name':'city';

        if($filter['operator'] == 'HAS_ANY' || $filter['operator'] == 'IS_ANY'){
            $this->query->whereHas($filter['key'], function ($query) use ($filter,$fieldName) {
                $query->whereIn($fieldName, $filter['value']);
            });
        }
        if($filter['operator'] == 'EXISTS'){
            $this->query->whereHas($filter['key']);
        }
        if($filter['operator'] == '='){
            $this->query->whereHas($filter['key'], function ($query) use ($filter,$fieldName) {
                $query->where($fieldName, $filter['value']);
            });
        }
        
    }
    private function attributevalidateFilter($filter)
    {
        $attribute = Attribute::where('name',$filter['attribute'])->first();
        if(!$attribute){
            throw new \Exception('Invalid attribute value');
        }
        $type=$attribute->type->value;

        switch ($type) {
            case 'text':
                $type='string';
            case 'select':
                $type='enum';
            default:
                 $type;
        }
        $arrayName = 'allowed'.$type.'Operators';	
        if(!in_array($filter['operator'], $this->$arrayName)){
            throw new \Exception('Invalid operator for type '.$type);
        }else{
            return $type;
        }
    }
    private function attributeFilter($filter)
    {
        //Validate if the attribute operator is valid
        $type = $this->attributevalidateFilter($filter);

        if($type=='number'){
            $filter['value']=(int)$filter['value'];
        };
        if($filter['operator'] == 'LIKE'){
            $filter['value']='%'.$filter['value'].'%';
        }
        if($filter['operator'] == 'IN'){
            $filter['value']=array_map('trim', explode(',', trim($filter['value'], '()')));  
            //filter the query depending on the operator and value
            $this->query->whereHas('attributes', function ($query) use ($filter) {
                $query->whereHas('attribute', function ($subQuery) use ($filter) {
                    $subQuery->where('name', $filter['attribute']);
                })->whereIn('value', $filter['value']);
            });          
        }else{
        //filter the query depending on the operator and value
        $this->query->whereHas('attributes', function ($query) use ($filter) {
            $query->whereHas('attribute', function ($subQuery) use ($filter) {
                $subQuery->where('name', $filter['attribute']);
            })->where('value', $filter['operator'], $filter['value']);
        });
        }

    }

//Apply filter conditions


public function applyFilters($filters, $with = [])
{
    //eager load relationships
    if (!empty($with)) {
        $this->query->with($with); 
    }
    $this->query->where(function ($query) use ($filters) {
        foreach ($filters as $filter) {
            // Main Logical Operator
            $mainOperator = $filter['operator']; 
            $conditionsList = $filter['conditions'];

    $query->{$mainOperator === 'AND' ? 'where' : 'orWhere'}(function ($subQuery) use ($conditionsList) {
        foreach ($conditionsList as $conditions) {
            // Handling nested conditions
            if (array_key_exists('logical_operator', $conditions)) { 
                $logicalOperator = $conditions['logical_operator']; 

                $subQuery->{$logicalOperator === 'AND' ? 'where' : 'orWhere'}(function ($nestedQuery) use ($conditions) {
                foreach ($conditions as $condition) {
                    // Process each condition
                    if (is_array($condition)) { 
                        $this->applyCondition($nestedQuery, $condition);
                    }
                    }
                    });
                    } else {
                         // Apply single condition
                        $this->applyCondition($subQuery, $conditions);
                    }
                }
            });
        }
    });

    return $this->query->get();
}


//function to apply individual conditions
private function applyCondition($query, $condition)
{
    $key = $condition['key']; 
    $filterType = $this->defineFilterType($key);
    $functionName = $filterType . 'Filter';
    // Call (field/relation/attribute) filter function
    $this->$functionName($condition);
}





}

