<?php
namespace IaC;

class Generator
{
    public static function dockerCompose(string $image = 'cajeerengine:4.0.0'): string
    {
        return "version: '3.9'\nservices:\n  app:\n    image: {$image}\n    ports:\n      - \"8080:80\"\n    environment:\n      - CE_REGION=local\n      - CE_CP_TOKEN=change_me\n      - REDIS_HOST=redis\n  redis:\n    image: redis:7\n";
    }

    public static function kubernetesDeployment(string $name = 'cajeerengine', string $image = 'cajeerengine:4.0.0'): string
    {
        return "apiVersion: apps/v1\nkind: Deployment\nmetadata:\n  name: {$name}\nspec:\n  replicas: 2\n  selector:\n    matchLabels:\n      app: {$name}\n  template:\n    metadata:\n      labels:\n        app: {$name}\n    spec:\n      containers:\n        - name: {$name}\n          image: {$image}\n          ports:\n            - containerPort: 80\n          env:\n            - name: CE_REGION\n              value: local\n            - name: CE_CP_TOKEN\n              valueFrom:\n                secretKeyRef:\n                  name: {$name}-secrets\n                  key: cp_token\n";
    }
}
