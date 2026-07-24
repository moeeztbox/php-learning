<?php

// Refactor: every controller (User, Role, Project, Task) repeated an identical
// handleRequest() switch that dispatches GET/POST/PUT/DELETE to getAll()/create()/
// update()/delete(). That dispatch logic now lives once here (Single Responsibility:
// this class is only responsible for HTTP-method dispatch and shared validation).
// Subclasses only implement the four abstract methods with their own repository calls.
abstract class BaseController
{
    public function handleRequest($method, $input)
    {
        switch ($method) {

            case "GET":
                return $this->getAll();

            case "POST":
                return $this->create($input);

            case "PUT":
                return $this->update($input);

            case "DELETE":
                return $this->delete($input);

            default:
                return [
                    "status" => 405,
                    "body" => ["message" => "Method Not Allowed"]
                ];
        }
    }

    // Refactor: reusable validation helper. Every create()/update() method used to repeat
    // a long isset() chain and hand-write its own "x, y and z are required" message.
    // This checks the given fields against $input and returns a ready-made 400 response
    // (or null when everything required is present), removing that duplication.
    protected function requireFields($input, array $fields)
    {
        foreach ($fields as $field) {
            if (!isset($input[$field])) {
                return [
                    "status" => 400,
                    "body" => ["message" => $this->formatRequiredFieldsMessage($fields)]
                ];
            }
        }

        return null;
    }

    // Builds the same "id is required" / "id, name and email are required" wording
    // the original hand-written messages used, so response text stays identical.
    private function formatRequiredFieldsMessage(array $fields)
    {
        if (count($fields) === 1) {
            return $fields[0] . " is required";
        }

        $last = array_pop($fields);

        return implode(", ", $fields) . " and " . $last . " are required";
    }

    abstract protected function getAll();
    abstract protected function create($input);
    abstract protected function update($input);
    abstract protected function delete($input);
}
